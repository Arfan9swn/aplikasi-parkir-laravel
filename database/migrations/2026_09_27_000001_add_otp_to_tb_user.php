<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The signup OTP proves the email is real before an admin may approve the
     * account; existing rows stay null because they predate the code.
     */
    public function up(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->string('otp_code', 60)->nullable()->after('email');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->timestamp('email_verified_at')->nullable()->after('otp_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at', 'email_verified_at']);
        });
    }
};
