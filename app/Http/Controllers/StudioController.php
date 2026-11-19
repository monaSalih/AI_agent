<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Studio;
use App\Models\StudioType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudioController extends Controller
{
    
    public function index()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            $studios = Studio::with(['category', 'type', 'provider'])->latest()->get();
        } elseif (Auth::check() && Auth::user()->role === 'provider') {
            $studios = Studio::with(['category', 'type'])
                ->where('provider_id', Auth::id())
                ->latest()
                ->get();
        } else {
            $studios = Studio::with(['category', 'type'])
                ->where('status', 'approved')
                ->where('is_active', true)
                ->latest()
                ->get();
        }

        return view('studios.index', compact('studios'));
    }

    
    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'provider') {
            abort(403, 'Only providers can add studios.');
        }

        $categories = Category::where('is_active', true)->get();
        $studioTypes = StudioType::orderBy('sort_order')->get();

        return view('studios.create', compact('categories', 'studioTypes'));
    }

    
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'provider') {
            abort(403, 'Only providers can add studios.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'studio_type_id' => 'required|exists:studio_types,id',
            'description' => 'required|string',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'area_sqm' => 'nullable|numeric|min:1',
            'hourly_price' => 'required|numeric|min:1',
            'facilities' => 'nullable|array',
            'included_equipment' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($request->name) . '-' . time();
        $validated['status'] = 'pending';
        $validated['is_active'] = true;
        $request->user()->studios()->create($validated);

        return redirect()->route('studios.index')
            ->with('status', 'Your studio was sent to the admin for approval.');
    }

   
    public function show(string $id)
    {
        $studio = Studio::with(['category', 'type', 'provider', 'images', 'services', 'packages'])->findOrFail($id);

        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $isOwner = Auth::check() && Auth::id() === $studio->provider_id;

        if ($studio->status !== 'approved' || !$studio->is_active) {
            if (!$isAdmin && !$isOwner) {
                abort(404);
            }
        }

        return view('studios.show', compact('studio'));
    }

   
    public function edit(string $id)
    {
        $studio = Studio::findOrFail($id);

        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $isOwner = Auth::check() && Auth::id() === $studio->provider_id;

        if (!$isAdmin && !$isOwner) {
            abort(403, 'You cannot edit this studio.');
        }

        $categories = Category::where('is_active', true)->get();
        $studioTypes = StudioType::orderBy('sort_order')->get();

        return view('studios.edit', compact('studio', 'categories', 'studioTypes'));
    }

    
    public function update(Request $request, string $id)
    {
        $studio = Studio::findOrFail($id);

        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $isOwner = Auth::check() && Auth::id() === $studio->provider_id;

        if (!$isAdmin && !$isOwner) {
            abort(403, 'You cannot edit this studio.');
        }

        
        if ($isAdmin) {
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected',
                'admin_note' => 'nullable|string|max:255',
            ]);

            $studio->update($validated);

            return redirect()->route('studios.index')
                ->with('status', 'Studio status updated.');
        }

       
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'studio_type_id' => 'required|exists:studio_types,id',
            'description' => 'required|string',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'area_sqm' => 'nullable|numeric|min:1',
            'hourly_price' => 'required|numeric|min:1',
            'facilities' => 'nullable|array',
            'included_equipment' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        
        if ($studio->status === 'rejected') {
            $validated['status'] = 'pending';
        }

        $studio->update($validated);

        return redirect()->route('studios.show', $studio->id)
            ->with('status', 'Studio updated.');
    }

    
    public function destroy(string $id)
    {
        $studio = Studio::findOrFail($id);

        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $isOwner = Auth::check() && Auth::id() === $studio->provider_id;

        if (!$isAdmin && !$isOwner) {
            abort(403, 'You cannot delete this studio.');
        }

        if ($studio->bookings()->count() > 0) {
            return redirect()->route('studios.index')
                ->with('error', 'This studio has bookings, you cannot delete it.');
        }

        $studio->delete();

        return redirect()->route('studios.index')
            ->with('status', 'Studio deleted.');
    }
}