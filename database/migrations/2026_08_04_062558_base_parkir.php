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
        Schema::create('tb_kendaraan', function (Blueprint $table) {
            $table->id('id_kendaraan');
            $table->id('id_user');
            $table->string('plat_nomor');
            $table->string('jenis_kendaraan');
            $table->string('warna');
            $table->string('pemilik');
        });

        Schema::create('tb_area_parkir', function (Blueprint $table) {
            $table->id('id_area');
            $table->string('nama_area');
            $table->int('kapasitas');
            $table->int('terisi');
        });

        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id('id_parkir');
            $table->id('id_kendaraan');
            $table->id('id_tarif');
            $table->id('id_user');
            $table->id('id_area');
            $table->date('waktu_masuk');
            $table->date('waktu_keluar');
            $table->int('durasi_jam');
            $table->decimal('biaya_total');
            $table->enum('status', ['masuk', 'keluar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
