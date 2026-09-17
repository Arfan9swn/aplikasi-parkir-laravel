<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Vehicle types used to be a fixed ENUM ('motor', 'mobil', 'lainnya'), so a
 * petugas could never introduce a new one from the UI.
 *
 * The column becomes a plain string: tb_tarif itself now defines the types that
 * exist, and tb_kendaraan / tb_transaksi keep matching a type by its name.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->string('jenis_kendaraan', 50)->change();
        });

        // One tariff per type. Skipped when legacy duplicates already exist:
        // the validator blocks new ones, and we must not delete tariff rows
        // that transactions still reference.
        $duplicates = DB::table('tb_tarif')
            ->select('jenis_kendaraan')
            ->groupBy('jenis_kendaraan')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if (! $duplicates) {
            Schema::table('tb_tarif', function (Blueprint $table) {
                $table->unique('jenis_kendaraan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('tb_tarif', function (Blueprint $table) {
                $table->dropUnique(['jenis_kendaraan']);
            });
        } catch (\Throwable $e) {
            // The index was never created (legacy duplicates) — nothing to drop.
        }

        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->enum('jenis_kendaraan', ['motor', 'mobil', 'lainnya'])->change();
        });
    }
};
