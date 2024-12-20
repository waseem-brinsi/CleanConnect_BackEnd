<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('addressee')->nullable();
            $table->string('phoneNumber');     // Phone column
            $table->string('role')->default('user');
            $table->string('email')->nullable()->change();
            $table->boolean('is_verified')->default(false);
            $table->string('verification_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['addressee', 'phoneNumber','is_verified','verification_code']);
        });
    }
};
