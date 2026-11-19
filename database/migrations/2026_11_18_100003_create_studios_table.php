<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('studio_type_id')->nullable()->constrained('studio_types')->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->text('description');
            $table->string('city', 100);
            $table->string('address', 255);
            $table->integer('capacity');
            $table->decimal('area_sqm', 8, 2)->nullable();
            $table->json('facilities')->nullable();
            $table->json('included_equipment')->nullable();
            $table->decimal('hourly_price', 10, 3);
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->boolean('is_active')->default(false);
            $table->string('admin_note', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('city');
            $table->index(['status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studios');
    }
};
