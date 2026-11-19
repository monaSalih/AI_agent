<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name', 150);
            $table->string('slug', 180)->unique();
            $table->string('tagline', 160)->nullable();
            $table->text('bio')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('logo', 500)->nullable();
            $table->string('cover', 500)->nullable();
            $table->string('instagram', 120)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('admin_note', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
