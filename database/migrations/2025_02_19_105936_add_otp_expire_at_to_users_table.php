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
            // Add 'otp' column, you can define a string type or integer type
            $table->string('otp')->nullable();  // Nullable in case no OTP is set initially
            $table->timestamp('otp_expires_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop 'otp' and 'otp_expires_at' columns if the migration is rolled back
            $table->dropColumn(['otp', 'otp_expires_at']);
        });
    }
};
