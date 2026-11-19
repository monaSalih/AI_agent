<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained('studios')->restrictOnDelete();
            $table->string('title', 150);
            $table->string('slug', 180)->unique();
            $table->string('specialty', 100);
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->enum('kind', ['core', 'addon', 'both'])->default('core');
            $table->enum('delivery_mode', ['at_studio', 'at_client', 'online'])->default('at_studio');
            $table->enum('pricing_type', ['hourly', 'fixed'])->default('hourly');
            $table->decimal('price', 10, 3);
            $table->integer('min_hours')->default(1);
            $table->integer('max_hours')->default(4);
            $table->boolean('price_includes_studio')->default(false);
            $table->string('resource_group', 80)->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('pending');
            $table->string('admin_note', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
