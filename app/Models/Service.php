<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'studio_id',
        'title',
        'slug',
        'specialty',
        'description',
        'image',
        'kind',
        'delivery_mode',
        'pricing_type',
        'price',
        'min_hours',
        'max_hours',
        'price_includes_studio',
        'resource_group',
        'is_active',
        'status',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'min_hours' => 'integer',
            'max_hours' => 'integer',
            'price_includes_studio' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_services', 'service_id', 'package_id')
            ->withTimestamps();
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}
