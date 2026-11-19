<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_id',
        'type',
        'provider',
        'provider_reference',
        'checkout_session_id',
        'amount',
        'currency',
        'status',
        'reason',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
            'processed_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function originalPayment(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'payment_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Transaction::class, 'payment_id');
    }
}
