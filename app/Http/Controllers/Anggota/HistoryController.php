<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\Kategori;
use App\Models\Pengajuan;
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

    public function editTagihan(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'jumlah' => 'required',
        ]);

        $tagihan = TransaksiT::with('pengajuan')->findOrFail($id);

        $jumlah = preg_replace('/[^0-9]/', '', (string) $request->jumlah);
        $jumlah = $jumlah === '' ? 0 : (int) $jumlah;

        $tagihan->jumlah = $jumlah;
        $tagihan->save();

        if ($tagihan->pengajuan) {
            $kategoriPinjaman = Kategori::where('nama', 'Pinjaman')->first();
            $idPinjaman = $kategoriPinjaman ? $kategoriPinjaman->id : 3;

            $totalBayarPinjaman = TransaksiT::where('id_pengajuan', $tagihan->pengajuan->id)
                ->where('id_kategori', $idPinjaman)
                ->sum('jumlah');

            $status = $totalBayarPinjaman >= (int) $tagihan->pengajuan->nominal_pinjaman ? 'sudah lunas' : 'belum lunas';
            Pengajuan::where('id', $tagihan->pengajuan->id)->update(['keterangan' => $status]);
        }

        return redirect('/histori/tagihan');
    }
}
