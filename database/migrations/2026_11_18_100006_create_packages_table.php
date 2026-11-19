<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained('studios')->restrictOnDelete();
            $table->string('name', 150);
            $table->string('type', 40)->default('portrait')->index();
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->enum('pricing_type', ['hourly', 'fixed'])->default('fixed');
            $table->decimal('price', 10, 3);
            $table->integer('min_hours')->default(1);
            $table->integer('max_hours')->default(4);
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['studio_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
