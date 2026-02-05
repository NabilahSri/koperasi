<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use App\Models\Kategori;
use App\Models\TransaksiS;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SimpananController extends Controller
{
    public function index()
    {
        $data['user'] = User::all();
        $data['kategori'] = Jenis::select('jenis.*', 'kategori.*')
            ->join('kategori', 'jenis.id', '=', 'kategori.id_jenis')
            ->where('jenis.nama', '=', 'Simpanan')
            ->where('kategori.nama', '!=', 'Iuran Pokok')
            ->get();
        $data['simpanan'] = TransaksiS::with('user')->with('kategori')->with('petugas')->orderBy('created_at', 'desc')->get();
        return view('pages.simpanan', $data);
    }

    public function create(Request $req)
    {
        $id_petugas = auth()->user()->id;
        $req->validate([
            'id_user' => 'required',
            'nama_penyetor' => 'required',
            'tanggal' => 'required',
            'keterangan' => 'required',
            'transaksi' => 'required|array',
        ]);

        $hasTransaction = false;

        foreach ($req->transaksi as $item) {
            if (isset($item['jumlah']) && $item['jumlah'] > 0) {
                TransaksiS::create([
                    'id_user' => $req->id_user,
                    'id_kategori' => $item['id_kategori'],
                    'id_petugas' => $id_petugas,
                    'nama_penyetor' => $req->nama_penyetor,
                    'jumlah' => $item['jumlah'],
                    'tanggal' => $req->tanggal,
                    'keterangan' => $req->keterangan,
                ]);
                $hasTransaction = true;
            }
        }

        if (!$hasTransaction) {
            return redirect()->back()->withErrors(['msg' => 'Harap isi minimal satu jumlah simpanan.']);
        }

        return redirect('/simpanan');
    }
    public function edit(Request $req)
    {
        $id_petugas = auth()->user()->id;
        $req->validate([
            'id_user' => 'required',
            'id_kategori' => 'required',
            'nama_penyetor' => 'required',
            'jumlah' => 'required',
            'tanggal' => 'required',
            'keterangan' => 'required',
        ]);
        $simpanan = TransaksiS::findOrFail($req->id);
        $simpanan->id_user = $req->id_user;
        $simpanan->id_kategori = $req->id_kategori;
        $simpanan->id_petugas = $id_petugas;
        $simpanan->nama_penyetor = $req->nama_penyetor;
        $simpanan->jumlah = $req->jumlah;
        $simpanan->tanggal = $req->tanggal;
        $simpanan->keterangan = $req->keterangan;
        $simpanan->save();
        return redirect('/simpanan');
    }

    public function delete($id)
    {
        $simpanan = TransaksiS::where('id', $id)->first();
        $simpanan->delete();
        return redirect('/simpanan');
    }

    public function getJumlah($id_user, $id_kat)
    {
        $users = User::where('id', $id_user)->first();
        return response()->json($users);
    }
}
