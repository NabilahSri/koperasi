<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
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

    public function create(Request $request)
    {
        $validasi = Pengajuan::where('id', $request->id)->first();

        $data = TransaksiT::create([
            'id_user' => $validasi->id_user,
            'id_pengajuan' => $validasi->id,
            'id_kategori' => $request->id_kategori,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        $tagihan = TransaksiT::where('id_pengajuan', $validasi->id)->get();

        $ttlpembayaran = 0;
        $sisapembayaran = 0;
        foreach ($tagihan as $tagihan) {
            if ($tagihan->id_pengajuan == $validasi->id &&  $tagihan->id_kategori ==  $request->id_kategori) {
                $ttlpembayaran +=  $tagihan->jumlah;
            }
        }

        // if ($request->id_kategori == 3) {
        //     $sisapembayaran = $validasi->nominal_pinjaman -  $ttlpembayaran;
        // } elseif ($request->id_kategori == 4) {
        //     $sisapembayaran = $validasi->nominal_bagihasil -  $ttlpembayaran;
        // }
        // if ($tagihan->jumlah == $sisapembayaran) {
        //     Pengajuan::where('id', $request->id)->update(['keterangan' => 'sudah lunas']);
        // }
        if ($ttlpembayaran >= $validasi->nominal_pinjaman + $validasi->nominal_bagihasil) {
            Pengajuan::where('id', $validasi->id)->update(['keterangan' => 'sudah lunas']);
        }

        return redirect('/tagihan/bayar');
    }
}
