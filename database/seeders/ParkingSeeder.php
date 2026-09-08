<?php

namespace Database\Seeders;

use App\Models\parkir_areas;
use App\Models\parkir_kendaraans;
use App\Models\parkir_tarifs;
use App\Models\parkir_transaksis;
use App\Models\parkir_users;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seed the parking application with demo reference data:
 * tariffs, parking areas, staff users and a handful of sample
 * vehicles / transactions so the ticketing screens have content.
 */
class ParkingSeeder extends Seeder
{
    public function run(): void
    {
        $tarifs = [];

        // ------------------------------------------------------------------
        // 1. Tariffs (per vehicle type, per hour)
        // ------------------------------------------------------------------
        $defaults = [
            'motor'   => 3000,
            'mobil'   => 5000,
            'lainnya' => 7000,
        ];

        foreach ($defaults as $jenis => $rate) {
            $tarif = parkir_tarifs::where('jenis_kendaraan', $jenis)->first();

            if (! $tarif) {
                $tarif = parkir_tarifs::create([
                    'jenis_kendaraan' => $jenis,
                    'tarif_per_jam'   => $rate,
                ]);
            }

            $tarifs[$jenis] = $tarif;
        }

        // ------------------------------------------------------------------
        // 2. Parking areas
        // ------------------------------------------------------------------
        $areas = [
            ['nama_area' => 'Area Utama',  'kapasitas' => 24, 'terisi' => 1],
            ['nama_area' => 'Area VIP',    'kapasitas' => 6,  'terisi' => 0],
            ['nama_area' => 'Area Motor',  'kapasitas' => 18, 'terisi' => 0],
            ['nama_area' => 'Area Servis', 'kapasitas' => 10, 'terisi' => 0],
        ];

        foreach ($areas as $data) {
            $area = parkir_areas::where('nama_area', $data['nama_area'])->first();

            if (! $area) {
                parkir_areas::create($data);
            }
        }

        // ------------------------------------------------------------------
        // 3. Staff users
        // ------------------------------------------------------------------
        $petugas = parkir_users::where('username', 'petugas')->first();

        if (! $petugas) {
            $petugas = parkir_users::create([
                'nama_lengkap' => 'Petugas Johari',
                'username'     => 'petugas',
                'password'     => Hash::make('password'),
                'role'         => 'petugas',
                'status_aktif' => 1,
            ]);
        }

        if (parkir_users::where('username', 'admin')->doesntExist()) {
            parkir_users::create([
                'nama_lengkap' => 'Admin Parkir',
                'username'     => 'admin',
                'password'     => Hash::make('password'),
                'role'         => 'admin',
                'status_aktif' => 1,
            ]);
        }

        // ------------------------------------------------------------------
        // 4. Sample vehicles + transactions (demo data)
        // ------------------------------------------------------------------
        if (parkir_transaksis::count() > 0) {
            return;
        }

        $areaUtama  = parkir_areas::where('nama_area', 'Area Utama')->first();
        $areaMotor  = parkir_areas::where('nama_area', 'Area Motor')->first();
        $areaServis = parkir_areas::where('nama_area', 'Area Servis')->first();

        $now = time();
        $at  = fn (int $hoursAgo): string => date('Y-m-d H:i:s', $now - ($hoursAgo * 3600));
// --- Vehicle 1: silver car -----------------------------------------
        $kendaraanA = parkir_kendaraans::create([
            'id_user'         => $petugas->getKey(),
            'plat_nomor'      => 'B 1234 ABC',
            'jenis_kendaraan' => 'mobil',
            'warna'           => 'Sinjiru (Silver)',
            'pemilik'         => 'Nanda Goyal',
        ]);

        // Completed 2-hour stay, 4 hours ago
        parkir_transaksis::create([
            'id_kendaraan' => $kendaraanA->getKey(),
            'id_tarif'     => $tarifs['mobil']->getKey(),
            'id_user'      => $petugas->getKey(),
            'id_area'      => $areaUtama->getKey(),
            'waktu_masuk'  => $at(4),
            'waktu_keluar' => $at(2),
            'durasi_jam'   => 2,
            'biaya_total'  => 2 * $tarifs['mobil']->tarif_per_jam,
            'status'       => 'keluar',
        ]);

        // Still parked: active ticket
        parkir_transaksis::create([
            'id_kendaraan' => $kendaraanA->getKey(),
            'id_tarif'     => $tarifs['mobil']->getKey(),
            'id_user'      => $petugas->getKey(),
            'id_area'      => $areaUtama->getKey(),
            'waktu_masuk'  => $at(-2),
            'waktu_keluar' => null,
            'durasi_jam'   => null,
            'biaya_total'  => null,
            'status'       => 'masuk',
        ]);

        // --- Vehicle 2: red motorcycle -------------------------------------
        $kendaraanB = parkir_kendaraans::create([
            'id_user'         => $petugas->getKey(),
            'plat_nomor'      => 'C 5678 XYZ',
            'jenis_kendaraan' => 'motor',
            'warna'           => 'Merah (Red)',
            'pemilik'         => 'Tom Hardy',
        ]);

        // Completed 1-hour stay, 9 hours ago
        parkir_transaksis::create([
            'id_kendaraan' => $kendaraanB->getKey(),
            'id_tarif'     => $tarifs['motor']->getKey(),
            'id_user'      => $petugas->getKey(),
            'id_area'      => $areaMotor->getKey(),
            'waktu_masuk'  => $at(9),
            'waktu_keluar' => $at(8),
            'durasi_jam'   => 1,
            'biaya_total'  => $tarifs['motor']->tarif_per_jam,
            'status'       => 'keluar',
        ]);

        // --- Vehicle 3: blue van -------------------------------------------
        $kendaraanC = parkir_kendaraans::create([
            'id_user'         => $petugas->getKey(),
            'plat_nomor'      => 'AB 1901 CD',
            'jenis_kendaraan' => 'lainnya',
            'warna'           => 'Biru (Blue)',
            'pemilik'         => 'Ria Sarij',
        ]);

        // Completed 3-hour stay, 2 days ago
        parkir_transaksis::create([
            'id_kendaraan' => $kendaraanC->getKey(),
            'id_tarif'     => $tarifs['lainnya']->getKey(),
            'id_user'      => $petugas->getKey(),
            'id_area'      => $areaServis->getKey(),
            'waktu_masuk'  => $at(50),
            'waktu_keluar' => $at(47),
            'durasi_jam'   => 3,
            'biaya_total'  => 3 * $tarifs['lainnya']->tarif_per_jam,
            'status'       => 'keluar',
        ]);
    }
}