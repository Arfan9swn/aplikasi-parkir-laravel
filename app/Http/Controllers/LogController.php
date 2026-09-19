<?php

namespace App\Http\Controllers;

use App\Models\parkir_logs;
use App\Models\parkir_users;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Server-rendered log hub — activity log + latest server log tail.
     */
    public function index(Request $request)
    {
        $role = session('auth_user.role') ?? '';
        $staff = in_array($role, ['admin', 'petugas', 'owner'], true);

        $q = trim((string) $request->query('q', ''));

        $query = parkir_logs::with('user');

        $allowed = ['waktu_aktivitas', 'petugas', 'aktivitas'];
        $sort    = SortHelper::field((string) $request->query('sort'), $allowed);
        $dir     = SortHelper::direction((string) $request->query('dir'));

        if ($q !== '') {
            $needle = strtolower($q);
            $query = $query->get()->filter(function ($l) use ($needle) {
                $name = strtolower((string) ($l->user ? $l->user->nama_lengkap : ''));
                $user = strtolower((string) ($l->user ? $l->user->username : ''));
                return str_contains(strtolower($l->aktivitas), $needle)
                    || str_contains($name, $needle)
                    || str_contains($user, $needle);
            })->values();
        } else {
            $query = $query->get();
        }

        $getters = [
            'waktu_aktivitas' => fn ($l) => (string) $l->waktu_aktivitas,
            'petugas'         => fn ($l) => (string) ($l->user->nama_lengkap ?? ''),
            'aktivitas'       => fn ($l) => (string) $l->aktivitas,
        ];
        $logs = $dir === 'asc'
            ? $query->sortBy($getters[$sort], SORT_REGULAR)->values()
            : $query->sortByDesc($getters[$sort])->values();
        $total = parkir_logs::count();
        $today = parkir_logs::where('waktu_aktivitas', 'like', date('Y-m-d') . '%')->count();
        $users = parkir_users::count();

        // System log tail (only for staff roles).
        $sysLog = $staff ? $this->tailSystemLog() : null;

        return view('aktivitas.index', [
            'logs'   => $logs,
            'q'      => $q,
            'total'  => $total,
            'today'  => $today,
            'users'  => $users,
            'sysLog' => $sysLog,
            'staff'  => $staff,
            'allowed' => $allowed,
        ]);
    }

    /**
     * Read the tail of the newest *.log file inside storage/logs.
     */
    private function tailSystemLog()
    {
        $dir = new \DirectoryIterator(storage_path('logs'));
        $candidate = null;

        foreach ($dir as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'log') {
                if (! $candidate || $fileInfo->getMTime() > $candidate->getMTime()) {
                    $candidate = clone $fileInfo;
                }
            }
        }

        if (! $candidate) {
            return null;
        }

        $path = $candidate->getPathname();
        $size = $candidate->getSize();

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

        return [
            'file' => $candidate->getFilename(),
            'size' => $size,
            'tail' => array_slice($lines, -300),
        ];
    }
}