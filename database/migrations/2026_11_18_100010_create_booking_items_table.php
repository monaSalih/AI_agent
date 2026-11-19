<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->restrictOnDelete();
            $table->enum('item_type', ['space', 'service', 'package', 'addon']);
            $table->foreignId('service_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('item_name', 150);
            $table->enum('pricing_type', ['hourly', 'fixed']);
            $table->decimal('unit_price', 10, 3);
            $table->decimal('line_total', 10, 3);
            $table->boolean('is_included')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['booking_id', 'service_id']);
            $table->unique(['booking_id', 'package_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
