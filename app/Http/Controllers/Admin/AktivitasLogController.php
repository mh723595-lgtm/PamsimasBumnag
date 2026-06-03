<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\User;
use Illuminate\Http\Request;

class AktivitasLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AktivitasLog::with('user')->latest();

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        } else {
            $query->whereDate('created_at', today());
        }

        // Filter user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter aksi
        if ($request->filled('aksi')) {
            $query->where('aksi', 'like', '%' . $request->aksi . '%');
        }

        // Filter role
        if ($request->filled('role')) {
            $query->whereHas('user', fn($q) => $q->where('role', $request->role));
        }

        $logs  = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        $stats = [
            'total_hari_ini' => AktivitasLog::whereDate('created_at', today())->count(),
            'login_hari_ini' => AktivitasLog::whereDate('created_at', today())->where('aksi', 'like', '%login%')->count(),
            'total_minggu'   => AktivitasLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'user_aktif'     => AktivitasLog::whereDate('created_at', today())->distinct('user_id')->count('user_id'),
        ];

        return view('admin.log.index', compact('logs', 'users', 'stats'));
    }
}