<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index()
    {
        $data['user'] = User::all();
        $data['pengajuan'] = Pengajuan::with('user')->get();
        return view('admin.pages.pengajuan', $data);
    }

    public function create(Request $req)
    {
        $req->validate([
            'id_user' => 'required',
            'nominal_pinjaman' => 'required',
            'propisi' => 'nullable',
            'nominal_bagihasil' => 'required',
            'tanggal_pengajuan' => 'required',
        ]);
        $nominal_pinjaman = preg_replace('/[^0-9]/', '', $req->nominal_pinjaman);
        $propisi = preg_replace('/[^0-9]/', '', (string) $req->propisi);
        $nominal_bagihasil = preg_replace('/[^0-9]/', '', $req->nominal_bagihasil);
        if ($propisi === '') {
            $propisi = (string) ((int) round(((int) $nominal_pinjaman) * 0.01));
        }
        Pengajuan::create([
            'id_user' => $req->id_user,
            'nominal_pinjaman' => $nominal_pinjaman,
            'propisi' => $propisi,
            'nominal_bagihasil' => $nominal_bagihasil,
            'tanggal_pengajuan' => $req->tanggal_pengajuan,
            'keterangan' => 'belum lunas',
        ]);
        return redirect('/tagihan/pengajuan');
    }

    public function edit(Request $req)
    {
        $req->validate([
            'id_user' => 'required',
            'nominal_pinjaman' => 'required',
            'propisi' => 'nullable',
            'nominal_bagihasil' => 'required',
            'tanggal_pengajuan' => 'required',
        ]);

        $nominal_pinjaman = preg_replace('/[^0-9]/', '', $req->nominal_pinjaman);
        $propisi = preg_replace('/[^0-9]/', '', (string) $req->propisi);
        $nominal_bagihasil = preg_replace('/[^0-9]/', '', $req->nominal_bagihasil);
        if ($propisi === '') {
            $propisi = (string) ((int) round(((int) $nominal_pinjaman) * 0.01));
        }

        Pengajuan::where('id', $req->id)->update([
            'id_user' => $req->id_user,
            'nominal_pinjaman' => $nominal_pinjaman,
            'propisi' => $propisi,
            'nominal_bagihasil' => $nominal_bagihasil,
            'tanggal_pengajuan' => $req->tanggal_pengajuan,
        ]);
        return redirect('/tagihan/pengajuan');
    }

    public function delete($id)
    {
        $pengajuan = Pengajuan::where('id', $id)->first();
        $pengajuan->delete();
        return redirect('/tagihan/pengajuan');
    }
}
