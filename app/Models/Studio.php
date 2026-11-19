<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Studio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'category_id',
        'studio_type_id',
        'name',
        'slug',
        'description',
        'city',
        'address',
        'capacity',
        'area_sqm',
        'facilities',
        'included_equipment',
        'hourly_price',
        'status',
        'is_active',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'area_sqm' => 'decimal:2',
            'hourly_price' => 'decimal:3',
            'facilities' => 'array',
            'included_equipment' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(StudioType::class, 'studio_type_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(StudioImage::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingItems(): HasManyThrough
    {
        return $this->hasManyThrough(BookingItem::class, Booking::class, 'studio_id', 'booking_id');
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Booking::class, 'studio_id', 'booking_id');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
}
