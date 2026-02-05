<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Pengajuan;
use App\Models\TransaksiS;
use App\Models\TransaksiT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data['jumlah_bayar_pinjaman'] = TransaksiT::where('id_kategori', 3)->where('id_user',  auth()->user()->id)->sum('jumlah');
        $data['jumlah_bayar_bagihasil'] = TransaksiT::where('id_kategori', 4)->where('id_user',  auth()->user()->id)->sum('jumlah');

        $data['pinjaman'] = Pengajuan::where('id_user',  auth()->user()->id)->first();
        if ($data['pinjaman']) {
            $data['sisa_pinjaman'] = $data['pinjaman']->nominal_pinjaman - $data['jumlah_bayar_pinjaman'];
        } else {
            $data['sisa_pinjaman'] = 0;
        }

        $data['bagihasil'] = Pengajuan::where('id_user',  auth()->user()->id)->first();
        if ($data['bagihasil']) {
            $data['sisa_bagihasil'] = $data['bagihasil']->nominal_bagihasil - $data['jumlah_bayar_bagihasil'];
        } else {
            $data['sisa_bagihasil'] = 0;
        }

        $kategoriPokok = Kategori::where('nama', 'Iuran Pokok')->first();
        $kategoriWajib = Kategori::where('nama', 'Iuran Wajib')->first();

        $data['total_iuran_pokok'] = $kategoriPokok ? TransaksiS::where('id_kategori', $kategoriPokok->id)->where('id_user',  auth()->user()->id)->sum('jumlah') : 0;
        $data['total_iuran_wajib'] = $kategoriWajib ? TransaksiS::where('id_kategori', $kategoriWajib->id)->where('id_user',  auth()->user()->id)->sum('jumlah') : 0;

        $data['total_admin'] = User::where('role', 'admin')->count();
        $data['total_anggota'] = User::where('role', 'anggota')->count();

        $data['total_semua_simpanan'] = TransaksiS::sum('jumlah');
        $data['total_semua_tagihan'] = Pengajuan::sum('nominal_pinjaman') + Pengajuan::sum('nominal_bagihasil');

        $tanggal = Carbon::now();
        $data['simpanan_masuk'] = TransaksiS::where('tanggal', $tanggal)->with('users')->get();


        return view('pages.dashboard', $data);
    }
}
