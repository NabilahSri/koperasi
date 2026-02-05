<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use App\Models\Kategori;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use App\Models\TransaksiT;
use App\Models\User;

class TagihanController extends Controller
{
    //
    public function index()
    {
        $data['pengajuan'] = Pengajuan::with('user')->where('keterangan', 'belum lunas')->get();
        $data['kategori'] = Jenis::select('jenis.*', 'kategori.*')
            ->join('kategori', 'jenis.id', '=', 'kategori.id_jenis')
            ->where('jenis.nama', '=', 'Tagihan')->get();
        $data['tagihan'] = TransaksiT::with('pengajuan')->get();
        return view('pages.tagihan', $data);
    }

    public function create(Request $request, $id)
    {
        $validasi = Pengajuan::where('id', $id)->first();

        if ($request->has('transaksi')) {
            foreach ($request->transaksi as $item) {
                $jumlah = preg_replace('/[^0-9]/', '', $item['jumlah']);
                if ($jumlah > 0) {
                    TransaksiT::create([
                        'id_user' => $validasi->id_user,
                        'id_pengajuan' => $validasi->id,
                        'id_kategori' => $item['id_kategori'],
                        'jumlah' => $jumlah,
                        'tanggal' => $request->tanggal,
                        'keterangan' => $request->keterangan,
                    ]);
                }
            }
        }

        $tagihan = TransaksiT::where('id_pengajuan', $validasi->id)->get();
        // $ttlpembayaran = $tagihan->sum('jumlah');

        // Cari ID kategori Pinjaman
        $kategoriPinjaman = Kategori::where('nama', 'Pinjaman')->first();
        $idPinjaman = $kategoriPinjaman ? $kategoriPinjaman->id : 3; // Default ke 3 jika tidak ditemukan

        $total_bayar_pinjaman = $tagihan->where('id_kategori', $idPinjaman)->sum('jumlah');

        if ($total_bayar_pinjaman >= $validasi->nominal_pinjaman) {
            Pengajuan::where('id', $validasi->id)->update(['keterangan' => 'sudah lunas']);
        }

        return redirect('/tagihan/bayar');
    }
}
