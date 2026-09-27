<?php

use App\Models\parkir_users;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Email becomes a required account field, so existing rows get a unique
     * placeholder address before the column stops accepting nulls.
     */
    public function up(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('nama_lengkap');
        });

        foreach (parkir_users::whereNull('email')->get() as $user) {
            $user->update(['email' => $user->username . '@parkir.test']);
        }

        Schema::table('tb_user', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
