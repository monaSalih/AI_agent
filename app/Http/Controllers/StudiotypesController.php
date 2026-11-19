<?php

namespace App\Http\Controllers;

use App\Models\StudioType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudiotypesController extends Controller
{
    
    public function index()
    {
        $studioTypes = StudioType::orderBy('sort_order')->get();
        return view('studiotypes.index', compact('studioTypes'));
    }

   
    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Only the admin can add studio types.');
        }

        return view('studiotypes.create');
    }

   
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Only the admin can add studio types.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:studio_types,name',
            'icon' => 'nullable|string|max:60',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('studio_types', 'public');
        }

        StudioType::create($validated);
        return redirect()->route('studiotypes.index');
    }

   
    public function show(string $id)
    {
        $studioType = StudioType::findOrFail($id);

        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'provider')) {
            $studios = $studioType->studios;
        } else {
            $studios = $studioType->studios()
                ->where('status', 'approved')
                ->where('is_active', true)
                ->get();
        }

        return view('studiotypes.show', compact('studioType', 'studios'));
    }

  
    public function edit(string $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Only the admin can edit studio types.');
        }

        $studioType = StudioType::findOrFail($id);
        return view('studiotypes.edit', compact('studioType'));
    }

    
    public function update(Request $request, string $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Only the admin can edit studio types.');
        }

        $studioType = StudioType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:studio_types,name,' . $studioType->id,
            'icon' => 'nullable|string|max:60',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($request->name);

        $oldImage = $studioType->image;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('studio_types', 'public');
        } else {
            unset($validated['image']);
        }

        $studioType->update($validated);

        if ($request->hasFile('image') && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('studiotypes.index');
    }

    
    public function destroy(string $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Only the admin can delete studio types.');
        }

        $studioType = StudioType::findOrFail($id);

        if ($studioType->studios()->count() > 0) {
            return redirect()->route('studiotypes.index')
                ->with('error', 'This type is used by studios, you cannot delete it.');
        }

        $studioType->delete();
        return redirect()->route('studiotypes.index');
    }
}