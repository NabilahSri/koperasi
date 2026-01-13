<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    //

    public function index()
    {
        $data['pengaturan'] = Lembaga::all();
        return view('pages.pengaturan', $data);
    }

    public function edit(Request $req)
    {
        $dataid = Lembaga::find($req->id);
        $Data = [
            'nama' => $req->nama,
            'pimpinan' => $req->pimpinan,
            'alamat' => $req->alamat,
            'nohp' => $req->nohp,
        ];

        if ($req->hasFile('logo')) {
            // Hapus foto lama jika ada
            if ($dataid->logo) {
                Storage::delete($dataid->logo);
            }

            // Simpan foto baru ke direktori penyimpanan yang sesuai
            $photoPath = $req->file('logo')->storeAs('logo', $req->nama . '.' . $req->file('logo')->getClientOriginalExtension());

            // Update path foto di database
            $Data['logo'] = $photoPath;
        }

        Lembaga::where('id', $req->id)->update($Data);

        return redirect('/pengaturan');
    }

}
