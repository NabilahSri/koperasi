<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\Kategori;
use App\Models\TransaksiS;
use App\Models\User;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    public function index(Request $request)
    {
        $data['user'] = User::active()->get();
        $data['kategori'] = Jenis::select('jenis.*', 'kategori.*')
            ->join('kategori', 'jenis.id', '=', 'kategori.id_jenis')
            ->where('jenis.nama', '=', 'Simpanan')
            ->where('kategori.nama', '!=', 'Iuran Pokok')
            ->get();
        $query = TransaksiS::with('user')->with('kategori')->with('petugas')
            ->whereHas('user', fn ($q) => $q->active())
            ->orderBy('created_at', 'desc');
        if ($request->filled('filter_user_id')) {
            $query->where('id_user', $request->filter_user_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        $data['simpanan'] = $query->get();
        $data['filter_user_id'] = $request->filter_user_id;
        $data['start_date'] = $request->start_date;
        $data['end_date'] = $request->end_date;
        return view('admin.pages.simpanan', $data);
    }

    public function create(Request $req)
    {
        $id_petugas = auth()->user()->id;
        $req->validate([
            'id_user' => 'required|exists:users,id,is_active,1',
            'nama_penyetor' => 'required',
            'tanggal' => 'required',
            'keterangan' => 'required',
            'transaksi' => 'required|array',
        ]);

        $hasTransaction = false;

        foreach ($req->transaksi as $item) {
            $jumlah = isset($item['jumlah']) ? preg_replace('/[^0-9]/', '', $item['jumlah']) : 0;
            if ($jumlah > 0) {
                TransaksiS::create([
                    'id_user' => $req->id_user,
                    'id_kategori' => $item['id_kategori'],
                    'id_petugas' => $id_petugas,
                    'nama_penyetor' => $req->nama_penyetor,
                    'jumlah' => $jumlah,
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
            'id_user' => 'required|exists:users,id,is_active,1',
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
        $users = User::active()->where('id', $id_user)->first();
        return response()->json($users);
    }
}
