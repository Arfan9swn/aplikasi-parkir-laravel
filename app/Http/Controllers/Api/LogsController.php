<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\parkir_logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logs = parkir_logs::with('user')->orderBy('waktu_aktivitas', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $logs
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required|integer|exists:tb_user,id_user',
            'aktivitas' => 'required|string',
            'waktu_aktivitas' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $log = parkir_logs::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Log aktivitas berhasil ditambahkan',
            'data' => $log
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $log = parkir_logs::with('user')->find($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Log aktivitas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $log
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $log = parkir_logs::find($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Log aktivitas tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_user' => 'sometimes|integer|exists:tb_user,id_user',
            'aktivitas' => 'sometimes|string',
            'waktu_aktivitas' => 'sometimes|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $log->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Log aktivitas berhasil diperbarui',
            'data' => $log
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $log = parkir_logs::find($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Log aktivitas tidak ditemukan'
            ], 404);
        }

        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Log aktivitas berhasil dihapus'
        ], 200);
    }

    /**
     * Monitor the raw server log files — tail the newest *.log file found
     * inside storage/logs (e.g. laravel.log) so operators can watch for
     * errors, warnings and stack traces without touching the terminal.
     * GET /api/system-logs
     */
    public function systemLog()
    {
        $dir   = new \DirectoryIterator(storage_path('logs'));
        $candidate = null;

        foreach ($dir as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'log') {
                if (!$candidate || $fileInfo->getMTime() > $candidate->getMTime()) {
                $candidate = clone $fileInfo;
                }
            }
        }

        if (!$candidate) {
            return response()->json([
                'success' => true,
                'data' => [
                    'file' => null,
                    'size' => 0,
                    'mtime' => null,
                ],
            ], 200);
        }

        $path  = $candidate->getPathname();
        $size  = $candidate->getSize();
        $mtime = $candidate->getMTime();

        // Read only the tail (last ~200KB) and keep the newest MAX_LINES lines.
        $lines = [];
        $handle = fopen($path, 'r');
        if ($handle) {
            $offset = max(0, $size - (200 * 1024));
            if ($offset > 0) {
                fseek($handle, $offset);
                fgets($handle); // drop the partial first line
            }
            while (($line = fgets($handle)) !== false) {
                $lines[] = rtrim($line, "\r\n");
            }
            fclose($handle);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'file'  => $candidate->getFilename(),
                'size'  => $size,
                'mtime' => $mtime,
                'tail'  => array_slice($lines, -300),
            ],
        ], 200);
    }
}