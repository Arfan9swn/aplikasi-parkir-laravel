<?php

use App\Models\parkir_areas;
use App\Models\parkir_users;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every parking area must have its own dedicated petugas, and a petugas
     * may only be assigned to a single area. The unique() index below
     * enforces that rule at the database level.
     */
    public function up(): void
    {
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            $table->integer('id_user')
                ->nullable()
                ->references('id_user')
                ->on('tb_user')
                ->unique();
        });

        // Backfill: assign a distinct petugas to every existing area.
        $existingPetugas = parkir_users::where('role', 'petugas')->get();
        $areas = parkir_areas::all();

        $used = 0;

        foreach ($areas as $area) {
            if ($area->id_user !== null) {
                continue;
            }

            $petugas = ($used < count($existingPetugas)) ? $existingPetugas[$used] : null;

            if (! $petugas) {
                $petugas = parkir_users::create([
                    'nama_lengkap' => 'Petugas Area ' . $area->nama_area,
                    'username'     => 'petugas_area_' . $area->id_area,
                    'password'     => Hash::make('password'),
                    'role'         => 'petugas',
                    'status_aktif' => 1,
                ]);
            }

            $area->update(['id_user' => $petugas->id_user]);
            $used++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            $table->dropUnique(['id_user']);
            $table->dropColumn('id_user');
        });
    }
};