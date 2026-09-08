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
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->datetime('waktu_keluar')->nullable()->change();
            $table->integer('durasi_jam')->nullable()->change();
            $table->decimal('biaya_total')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->datetime('waktu_keluar')->change();
            $table->integer('durasi_jam')->change();
            $table->decimal('biaya_total')->change();
        });
    }
};