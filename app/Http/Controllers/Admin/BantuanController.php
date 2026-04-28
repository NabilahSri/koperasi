<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bantuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BantuanController extends Controller
{
    public function index()
    {
        if (!in_array(auth()->user()->role, ['admin', 'operator'])) {
            abort(403);
        }

        $data['anggota'] = User::where('role', 'anggota')->active()->orderBy('name')->get();
        $data['bantuanNames'] = Bantuan::query()
            ->select('nama_bantuan')
            ->distinct()
            ->orderBy('nama_bantuan')
            ->pluck('nama_bantuan');

        $data['riwayat'] = Bantuan::with(['user', 'petugas'])
            ->whereHas('user', fn ($q) => $q->active())
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $data['riwayatForJs'] = $data['riwayat']
            ->map(function (Bantuan $b) {
                return [
                    'id' => $b->id,
                    'id_user' => $b->id_user,
                    'nama_bantuan' => $b->nama_bantuan,
                    'jumlah' => $b->jumlah,
                    'tanggal' => $b->tanggal,
                    'keterangan' => $b->keterangan,
                    'user_name' => optional($b->user)->name,
                    'petugas_name' => optional($b->petugas)->name,
                ];
            })
            ->values();

        $data['rekap'] = DB::table('bantuan')
            ->join('users', 'bantuan.id_user', '=', 'users.id')
            ->where('users.is_active', 1)
            ->select('bantuan.id_user', 'bantuan.nama_bantuan', DB::raw('SUM(bantuan.jumlah) AS total_jumlah'))
            ->groupBy('bantuan.id_user', 'bantuan.nama_bantuan')
            ->orderBy('nama_bantuan')
            ->get();

        $userNameById = $data['anggota']->keyBy('id')->map(fn($u) => $u->name);
        foreach ($data['rekap'] as $row) {
            $row->nama_anggota = $userNameById[$row->id_user] ?? '-';
        }

        return view('admin.pages.bantuan', $data);
    }

    public function create(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'operator'])) {
            abort(403);
        }

        $validated = $request->validate([
            'id_user' => 'required|exists:users,id,is_active,1',
            'nama_bantuan' => 'required|string|max:255',
            'jumlah' => 'required',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $jumlah = preg_replace('/[^0-9]/', '', (string) $validated['jumlah']);
        $jumlah = $jumlah === '' ? 0 : (int) $jumlah;

        Bantuan::create([
            'id_user' => (int) $validated['id_user'],
            'id_petugas' => Auth::id(),
            'nama_bantuan' => $validated['nama_bantuan'],
            'jumlah' => $jumlah,
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect('/bantuan');
    }
}
