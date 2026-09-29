<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('roll_number', 50)->unique();
            $table->string('program', 100)->default('B.Tech CSE');
            $table->unsignedSmallInteger('batch')->nullable();
            $table->decimal('cgpa', 3, 2)->nullable();
            $table->boolean('public_display_consent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
