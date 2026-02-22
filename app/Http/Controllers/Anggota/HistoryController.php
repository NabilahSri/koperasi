<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\TransaksiS;
use App\Models\TransaksiT;
use App\Models\PengambilanSimpanan;
use App\Models\User;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function simpanan()
    {
        $data['user'] = User::all();
        $data['kategori'] = Jenis::select('jenis.*', 'kategori.*')
            ->join('kategori', 'jenis.id', '=', 'kategori.id_jenis')
            ->where('jenis.nama', '=', 'Simpanan')
            ->get();
        $data['simpanan'] = TransaksiS::with('user')->with('kategori')->where('id_user',  auth()->user()->id)->get();
        return view('anggota.pages.historisimpanan', $data);
    }

    public function tagihan()
    {
        $data['user'] = User::all();
        $data['kategori'] = Jenis::select('jenis.*', 'kategori.*')
            ->join('kategori', 'jenis.id', '=', 'kategori.id_jenis')
            ->where('jenis.nama', '=', 'Tagihan')
            ->get();

        $query = TransaksiT::with('users')->with('kategori');
        if (auth()->user()->role != 'admin') {
            $query->where('id_user', auth()->user()->id);
        }
        $data['tagihan'] = $query->get();

        return view('anggota.pages.historitagihan', $data);
    }

    public function pengambilan()
    {
        $data['pengambilan'] = PengambilanSimpanan::with(['user', 'kategori', 'petugas'])
            ->where('id_user', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('anggota.pages.historipengambilan', $data);
    }
}
