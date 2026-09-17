<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Guest reservations: non-logged-in visitors can only reserve a spot in an
 * area; staff review the requests from /reservasi/daftar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_reservasi', function (Blueprint $table) {
            $table->increments('id_reservasi');
            // tb_area_parkir.id_area is bigint unsigned ($table->id() in the
            // base migration) — the FK requires the same type on this side.
            $table->unsignedBigInteger('id_area');
            $table->string('plat_nomor', 20);
            $table->string('pemilik', 100)->nullable();
            $table->string('kontak', 100)->nullable();
            $table->dateTime('waktu_datang');
            $table->string('status', 20)->default('menunggu'); // menunggu|dikonfirmasi|dibatalkan
            $table->dateTime('waktu_pengajuan');
            $table->foreign('id_area')->references('id_area')->on('tb_area_parkir');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_reservasi');
    }
};
