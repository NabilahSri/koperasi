<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\Kategori;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use App\Models\TransaksiT;
use App\Models\User;

class TagihanController extends Controller
{
    public function index()
    {
        $data['pengajuan'] = Pengajuan::with('user')
            ->where('keterangan', 'belum lunas')
            ->whereHas('user', fn ($q) => $q->active())
            ->get();
        $data['kategori'] = Jenis::select('jenis.*', 'kategori.*')
            ->join('kategori', 'jenis.id', '=', 'kategori.id_jenis')
            ->where('jenis.nama', '=', 'Tagihan')->get();
        $data['tagihan'] = TransaksiT::with(['pengajuan', 'users'])
            ->whereHas('users', fn ($q) => $q->active())
            ->get();
        return view('admin.pages.tagihan', $data);
    }

    public function create(Request $request, $id)
    {
        $validasi = Pengajuan::with('user')->where('id', $id)->first();
        if (!$validasi || !$validasi->user || !$validasi->user->is_active) {
            return redirect('/tagihan/bayar')->with('error', 'User tidak aktif.');
        }

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

        $kategoriPinjaman = Kategori::where('nama', 'Pinjaman')->first();
        $idPinjaman = $kategoriPinjaman ? $kategoriPinjaman->id : 3;

        $total_bayar_pinjaman = $tagihan->where('id_kategori', $idPinjaman)->sum('jumlah');

        if ($total_bayar_pinjaman >= $validasi->nominal_pinjaman) {
            Pengajuan::where('id', $validasi->id)->update(['keterangan' => 'sudah lunas']);
        }

        return redirect('/tagihan/bayar');
    }
}
