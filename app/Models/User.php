<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'city',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function providerProfile(): HasOne
    {
        return $this->hasOne(ProviderProfile::class);
    }

    public function studios(): HasMany
    {
        return $this->hasMany(Studio::class, 'provider_id');
    }

    public function services(): HasManyThrough
    {
        return $this->hasManyThrough(Service::class, Studio::class, 'provider_id', 'studio_id');
    }

    public function packages(): HasManyThrough
    {
        return $this->hasManyThrough(Package::class, Studio::class, 'provider_id', 'studio_id');
    }

    public function availabilities(): HasManyThrough
    {
        return $this->hasManyThrough(Availability::class, Studio::class, 'provider_id', 'studio_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function providerBookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Studio::class, 'provider_id', 'studio_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function pageVisits(): HasMany
    {
        return $this->hasMany(PageVisit::class);
    }
}
