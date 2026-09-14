<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\parkir_areas;
use App\Models\parkir_transaksis;
use App\Models\parkir_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = parkir_areas::with('petugas')->get();
        return response()->json([
            'success' => true,
            'data' => $areas
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_area' => 'required|string|unique:tb_area_parkir,nama_area',
            'kapasitas' => 'required|integer|min:1',
            'terisi' => 'required|integer|min:0',
            'id_user' => 'required|integer|exists:tb_user,id_user'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $petugasError = $this->petugasError($request->id_user, null);
        if ($petugasError) {
            return response()->json([
                'success' => false,
                'errors' => ['id_user' => [$petugasError]]
            ], 422);
        }

        $area = parkir_areas::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Area parkir berhasil ditambahkan',
            'data' => $area->fresh(['petugas'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $area = parkir_areas::with('petugas', 'transaksis')->find($id);

        if (!$area) {
            return response()->json([
                'success' => false,
                'message' => 'Area parkir tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $area
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $area = parkir_areas::find($id);

        if (!$area) {
            return response()->json([
                'success' => false,
                'message' => 'Area parkir tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_area' => 'sometimes|string|unique:tb_area_parkir,nama_area,' . $id . ',id_area',
            'kapasitas' => 'sometimes|integer|min:1',
            'terisi' => 'sometimes|integer|min:0',
            'id_user' => 'sometimes|integer|exists:tb_user,id_user'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('id_user')) {
            $petugasError = $this->petugasError($request->id_user, $id);
            if ($petugasError) {
                return response()->json([
                    'success' => false,
                    'errors' => ['id_user' => [$petugasError]]
                ], 422);
            }
        }

        $area->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Area parkir berhasil diperbarui',
            'data' => $area->fresh(['petugas'])
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $area = parkir_areas::find($id);

        if (!$area) {
            return response()->json([
                'success' => false,
                'message' => 'Area parkir tidak ditemukan'
            ], 404);
        }

        if (parkir_transaksis::where('id_area', $area->id_area)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Area tidak dapat dihapus karena masih memiliki riwayat transaksi.'
            ], 422);
        }

        $area->delete();

        return response()->json([
            'success' => true,
            'message' => 'Area parkir berhasil dihapus'
        ], 200);
    }

    /**
     * Validate that the selected user is a dedicated, active petugas
     * and is not already assigned to another area.
     */
    private function petugasError($idUser, $excludeAreaId)
    {
        $petugas = parkir_users::find($idUser);

        if (! $petugas || $petugas->role !== 'petugas') {
            return 'Petugas yang dipilih tidak valid — pilih akun dengan role petugas.';
        }

        if ((int) $petugas->status_aktif !== 1) {
            return 'Petugas yang dipilih sedang dinonaktifkan.';
        }

        $assigned = parkir_areas::where('id_user', $petugas->id_user);

        if ($excludeAreaId) {
            $assigned = $assigned->where('id_area', '!=', $excludeAreaId);
        }

        if ($assigned->exists()) {
            return 'Petugas tersebut sudah ditugaskan ke area lain. Setiap area memerlukan petugas yang berbeda.';
        }

        return null;
    }
}