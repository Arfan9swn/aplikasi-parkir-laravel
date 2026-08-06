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
            $table->integer('id_user')->references('id_user')->on('tb_user');
            $table->string('plat_nomor')->unique();
            $table->string('jenis_kendaraan');
            $table->string('warna');
            $table->string('pemilik');
        });

        Schema::create('tb_area_parkir', function (Blueprint $table) {
            $table->id('id_area');
            $table->string('nama_area')->unique();
            $table->integer('kapasitas');
            $table->integer('terisi');
        });

        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id('id_parkir');
            $table->integer('id_kendaraan')->references('id_kendaraan')->on('tb_kendaraan');
            $table->integer('id_tarif')->references('id_tarif')->on('tb_tarif');
            $table->integer('id_user')->references('id_user')->on('tb_user');
            $table->integer('id_area')->references('id_area')->on('tb_area_parkir');
            $table->datetime('waktu_masuk');
            $table->datetime('waktu_keluar');
            $table->integer('durasi_jam');
            $table->decimal('biaya_total');
            $table->enum('status', ['masuk', 'keluar']);
        });

        Schema::create('tb_log_aktivitas', function (Blueprint $table) {
            $table->id('id_log');
            $table->integer('id_user')->references('id_user')->on('tb_user');
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
            $table->string('username')->unique();
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
