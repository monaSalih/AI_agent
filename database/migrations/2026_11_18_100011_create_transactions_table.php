<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('transactions')->restrictOnDelete();
            $table->enum('type', ['payment', 'refund']);
            $table->string('provider', 30)->default('stripe');
            $table->string('provider_reference', 255)->nullable();
            $table->string('checkout_session_id', 255)->nullable()->unique();
            $table->decimal('amount', 10, 3);
            $table->char('currency', 3)->default('JOD');
            $table->enum('status', ['pending', 'processing', 'succeeded', 'failed', 'cancelled'])->default('pending');
            $table->string('reason', 255)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_reference']);
            $table->index(['booking_id', 'type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
