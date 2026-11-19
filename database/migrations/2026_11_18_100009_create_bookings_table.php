<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('studio_id')->constrained('studios')->restrictOnDelete();
            $table->enum('mode', ['space', 'service', 'package', 'custom']);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->decimal('total_price', 10, 3);
            $table->char('currency', 3)->default('JOD');
            $table->enum('status', ['pending_payment', 'confirmed', 'completed', 'cancelled', 'expired'])->default('pending_payment');
            $table->dateTime('payment_expires_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
            $table->index(['studio_id', 'status', 'starts_at', 'ends_at']);
            $table->index(['status', 'payment_expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
