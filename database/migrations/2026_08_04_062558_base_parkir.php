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
            $table->integer('kapasitas');
            $table->integer('terisi');
        });

        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id('id_parkir');
            $table->id('id_kendaraan');
            $table->id('id_tarif');
            $table->id('id_user');
            $table->id('id_area');
            $table->datetime('waktu_masuk');
            $table->datetime('waktu_keluar');
            $table->integer('durasi_jam');
            $table->decimal('biaya_total');
            $table->enum('status', ['masuk', 'keluar']);
        });

        Schema::create('tb_log_aktivitas', function (Blueprint $table) {
            $table->id('id_log');
            $table->id('id_user');
            $table->string('aktivitas');
            $table->datetime('waktu_aktivitas');
        });

        Schema::create('tb_tarif', function (Blueprint $table) {
            $table->id('id_tarif');
            $table->enum('jenis_kendaraan', ['motor', 'mobil', 'lainnya']);
            $table->decimal('tarif_per_jam');
        });

        Schema::create('tb_user', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama_lengkap');
            $table->string('username');
            $table->string('password');
            $table->enum('role', ['petugas', 'admin', 'owner']);
            $table->tinyInteger('status_aktif');
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
