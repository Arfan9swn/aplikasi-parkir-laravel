<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\parkir_tarifs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TarifsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tarifs = parkir_tarifs::all();
        return response()->json([
            'success' => true,
            'data' => $tarifs
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_kendaraan' => 'required|in:motor,mobil,lainnya',
            'tarif_per_jam' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tarif = parkir_tarifs::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil ditambahkan',
            'data' => $tarif
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tarif = parkir_tarifs::with('transaksis')->find($id);

        if (!$tarif) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tarif
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tarif = parkir_tarifs::find($id);

        if (!$tarif) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'jenis_kendaraan' => 'sometimes|in:motor,mobil,lainnya',
            'tarif_per_jam' => 'sometimes|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tarif->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil diperbarui',
            'data' => $tarif
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tarif = parkir_tarifs::find($id);

        if (!$tarif) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif tidak ditemukan'
            ], 404);
        }

        $tarif->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarif berhasil dihapus'
        ], 200);
    }
}