<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index(){
        $data['user'] = User::all();
        $data['pengajuan'] = Pengajuan::with('user')->get();
        return view('pages.pengajuan', $data);
    }

    public function create(Request $req){
        $req->validate([
            'id_user' => 'required',
            'nominal_pinjaman' => 'required',
            'nominal_bagihasil' => 'required',
            'tanggal_pengajuan' => 'required',
        ]);
        Pengajuan::create([
            'id_user' => $req->id_user,
            'nominal_pinjaman' => $req->nominal_pinjaman,
            'nominal_bagihasil' => $req->nominal_bagihasil,
            'tanggal_pengajuan' => $req->tanggal_pengajuan,
            'keterangan' => 'belum lunas',
        ]);
        return redirect('/tagihan/pengajuan');
    }

    public function edit(Request $req){
        $req->validate([
            'id_user' => 'required',
            'nominal_pinjaman' => 'required',
            'nominal_bagihasil' => 'required',
            'tanggal_pengajuan' => 'required',
            'keterangan' => 'required',
        ]);
        Pengajuan::where('id',$req->id)->update([
            'id_user' => $req->id_user,
            'nominal_pinjaman' => $req->nominal_pinjaman,
            'nominal_bagihasil' => $req->nominal_bagihasil,
            'tanggal_pengajuan' => $req->tanggal_pengajuan,
            'keterangan' => $req->keterangan,
        ]);
        return redirect('/tagihan/pengajuan');
    }

    public function delete($id){
        $pengajuan = Pengajuan::where('id',$id)->first();
        $pengajuan->delete();
        return redirect('/tagihan/pengajuan');
    }
}
