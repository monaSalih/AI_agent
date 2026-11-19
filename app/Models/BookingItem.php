<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'item_type',
        'service_id',
        'package_id',
        'item_name',
        'pricing_type',
        'unit_price',
        'line_total',
        'is_included',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:3',
            'line_total' => 'decimal:3',
            'is_included' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
