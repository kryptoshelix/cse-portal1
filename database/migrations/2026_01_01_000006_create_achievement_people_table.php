<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievement_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('participant_name');
            $table->string('role_in_achievement', 100)->nullable();
            $table->timestamps();
            $table->unique(['achievement_id', 'participant_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_people');
    }
};
