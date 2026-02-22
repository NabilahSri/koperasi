<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $data['pengaturan'] = Lembaga::all();
        return view('admin.pages.pengaturan', $data);
    }

    public function edit(Request $req)
    {
        $dataid = Lembaga::find($req->id);
        $Data = [
            'nama' => $req->nama,
            'pimpinan' => $req->pimpinan,
            'alamat' => $req->alamat,
            'nohp' => $req->nohp,
            'tenggat_iuran_wajib' => $req->tenggat_iuran_wajib,
            'tenggat_bayar_tagihan' => $req->tenggat_bayar_tagihan,
        ];

        if ($req->hasFile('logo')) {
            if ($dataid->logo) {
                Storage::delete($dataid->logo);
            }
            $photoPath = $req->file('logo')->storeAs('logo', $req->nama . '.' . $req->file('logo')->getClientOriginalExtension());
            $Data['logo'] = $photoPath;
        }

        Lembaga::where('id', $req->id)->update($Data);

        return redirect('/pengaturan');
    }
}
