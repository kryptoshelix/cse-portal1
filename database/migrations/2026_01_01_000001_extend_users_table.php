<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('student')->after('password');
            $table->string('status', 20)->default('pending')->after('role');
            $table->boolean('can_review_achievements')->default(false)->after('status');
            $table->string('phone', 30)->nullable()->after('can_review_achievements');
            $table->string('address')->nullable()->after('phone');
            $table->index(['role', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'status']);
            $table->dropColumn(['role', 'status', 'can_review_achievements', 'phone', 'address']);
        });
    }
};
