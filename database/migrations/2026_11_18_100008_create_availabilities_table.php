<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('type', ['weekly', 'blocked']);
            $table->tinyInteger('day_of_week')->nullable(); 
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('reason', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['studio_id', 'type', 'day_of_week']);
            $table->index(['service_id', 'type', 'day_of_week']);
            $table->index(['studio_id', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
