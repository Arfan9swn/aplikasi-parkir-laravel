<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Self-registered accounts must be approved by an admin before they can use
     * the worker panel. Existing rows default to 'diterima' so accounts created
     * before this migration keep their access.
     */
    public function up(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['menunggu', 'diterima', 'ditolak'])
                ->default('diterima')
                ->after('status_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropColumn('status_verifikasi');
        });
    }
};
