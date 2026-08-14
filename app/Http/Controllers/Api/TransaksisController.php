<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\parkir_areas;
use App\Models\parkir_tarifs;
use App\Models\parkir_transaksis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksis = parkir_transaksis::with('kendaraan', 'tarif', 'user', 'area')->get();
        return response()->json([
            'success' => true,
            'data' => $transaksis
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_kendaraan' => 'required|integer|exists:tb_kendaraan,id_kendaraan',
            'id_tarif' => 'required|integer|exists:tb_tarif,id_tarif',
            'id_user' => 'required|integer|exists:tb_user,id_user',
            'id_area' => 'required|integer|exists:tb_area_parkir,id_area',
            'waktu_masuk' => 'required|date',
            'waktu_keluar' => 'nullable|date|after_or_equal:waktu_masuk',
            'status' => 'required|in:masuk,keluar'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $area = parkir_areas::find($request->id_area);

        if ($request->status === 'masuk') {
            if ($area->terisi >= $area->kapasitas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area parkir sudah penuh'
                ], 422);
            }
        }

        $data = $request->all();

        if ($request->waktu_keluar) {
            $waktuMasuk = new \DateTime($request->waktu_masuk);
            $waktuKeluar = new \DateTime($request->waktu_keluar);
            $durasi = $waktuMasuk->diff($waktuKeluar);

            $durasiJam = max(1, ceil(($durasi->h + ($durasi->i / 60) + ($durasi->s / 3600))));

            $tarif = parkir_tarifs::find($request->id_tarif);
            $biayaTotal = $durasiJam * $tarif->tarif_per_jam;

            $data['durasi_jam'] = $durasiJam;
            $data['biaya_total'] = $biayaTotal;
        }

        $transaksi = parkir_transaksis::create($data);

        if ($request->status === 'masuk') {
            $area->increment('terisi');
        } else {
            $area->decrement('terisi');
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditambahkan',
            'data' => $transaksi
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = parkir_transaksis::with('kendaraan', 'tarif', 'user', 'area')->find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaksi = parkir_transaksis::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_kendaraan' => 'sometimes|integer|exists:tb_kendaraan,id_kendaraan',
            'id_tarif' => 'sometimes|integer|exists:tb_tarif,id_tarif',
            'id_user' => 'sometimes|integer|exists:tb_user,id_user',
            'id_area' => 'sometimes|integer|exists:tb_area_parkir,id_area',
            'waktu_masuk' => 'sometimes|date',
            'waktu_keluar' => 'sometimes|date|after_or_equal:waktu_masuk',
            'durasi_jam' => 'sometimes|integer|min:1',
            'biaya_total' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:masuk,keluar'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        if ($request->has('waktu_keluar') && $request->waktu_keluar) {
            $waktuMasuk = new \DateTime($request->waktu_masuk ?? $transaksi->waktu_masuk);
            $waktuKeluar = new \DateTime($request->waktu_keluar);
            $durasi = $waktuMasuk->diff($waktuKeluar);

            $durasiJam = max(1, ceil(($durasi->h + ($durasi->i / 60) + ($durasi->s / 3600))));

            $tarifId = $request->id_tarif ?? $transaksi->id_tarif;
            $tarif = parkir_tarifs::find($tarifId);
            $biayaTotal = $durasiJam * $tarif->tarif_per_jam;

            $data['durasi_jam'] = $durasiJam;
            $data['biaya_total'] = $biayaTotal;
        }

        if ($request->has('status') && $request->status !== $transaksi->status) {
            $area = parkir_areas::find($request->id_area ?? $transaksi->id_area);
            if ($request->status === 'masuk') {
                $area->increment('terisi');
            } else {
                $area->decrement('terisi');
            }
        }

        $transaksi->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui',
            'data' => $transaksi
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = parkir_transaksis::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        if ($transaksi->status === 'masuk') {
            $area = parkir_areas::find($transaksi->id_area);
            if ($area && $area->terisi > 0) {
                $area->decrement('terisi');
            }
        }

        $transaksi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus'
        ], 200);
    }
}