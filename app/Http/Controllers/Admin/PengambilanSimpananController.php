<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiS;
use App\Models\PengambilanSimpanan;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengambilanSimpananController extends Controller
{
    public function index()
    {
        $withdrawals = PengambilanSimpanan::with(['user', 'kategori', 'petugas'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPengambilan = PengambilanSimpanan::sum('jumlah');
        $pengambilanHariIni = PengambilanSimpanan::whereDate('tanggal', now())->sum('jumlah');
        $pengambilanBulanIni = PengambilanSimpanan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        $users = User::all();

        return view('admin.pages.pengambilan.index', compact('withdrawals', 'totalPengambilan', 'pengambilanHariIni', 'pengambilanBulanIni', 'users'));
    }

    public function create()
    {
        $users = User::where('role', 'anggota')->get();
        $kategoris = Kategori::whereIn('nama', ['Manasuka', 'Lebaran', 'Simpanan Manasuka', 'Simpanan Lebaran'])->get();

        return view('admin.pages.pengambilan.create', compact('users', 'kategoris'));
    }

    public function getSaldo($userId, $kategoriId)
    {
        $masuk = TransaksiS::where('id_user', $userId)
            ->where('id_kategori', $kategoriId)
            ->sum('jumlah');
        $keluar = PengambilanSimpanan::where('id_user', $userId)
            ->where('id_kategori', $kategoriId)
            ->sum('jumlah');
        $saldo = $masuk - $keluar;

        return response()->json(['saldo' => $saldo]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_kategori' => 'required|exists:kategori,id',
            'jumlah' => 'required',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $amount = (int) preg_replace('/[^0-9]/', '', $request->jumlah);
        if ($amount < 1) {
            return back()->with('error', 'Nominal tidak valid.')->withInput();
        }

        $masuk = TransaksiS::where('id_user', $request->id_user)
            ->where('id_kategori', $request->id_kategori)
            ->sum('jumlah');
        $keluar = PengambilanSimpanan::where('id_user', $request->id_user)
            ->where('id_kategori', $request->id_kategori)
            ->sum('jumlah');
        $currentBalance = $masuk - $keluar;

        if ($amount > $currentBalance) {
            return back()->with('error', 'Saldo tidak mencukupi. Saldo saat ini: Rp ' . number_format($currentBalance, 0, ',', '.'))->withInput();
        }

        DB::beginTransaction();
        try {
            $user = User::find($request->id_user);

            PengambilanSimpanan::create([
                'id_user' => $request->id_user,
                'id_kategori' => $request->id_kategori,
                'id_petugas' => Auth::id(),
                'nama_pengambil' => $request->nama_penyetor ?? $user->name,
                'jumlah' => $amount,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan ?? 'Pengambilan Tabungan',
            ]);

            DB::commit();
            return redirect()->route('pengambilan.index')->with('success', 'Pengambilan tabungan berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
