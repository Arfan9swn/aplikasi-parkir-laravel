<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\parkir_areas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = parkir_areas::all();
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
            'terisi' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $area = parkir_areas::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Area parkir berhasil ditambahkan',
            'data' => $area
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $area = parkir_areas::with('transaksis')->find($id);

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
            'terisi' => 'sometimes|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $area->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Area parkir berhasil diperbarui',
            'data' => $area
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

        $area->delete();

        return response()->json([
            'success' => true,
            'message' => 'Area parkir berhasil dihapus'
        ], 200);
    }
}