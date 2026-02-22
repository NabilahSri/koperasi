<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use Illuminate\Http\Request;

class JenisController extends Controller
{
    public function index()
    {
        $data['jenis'] = Jenis::all();
        return view('admin.pages.jenis', $data);
    }

    public function create(Request $req)
    {
        $data = $req->validate([
            'nama' => 'required'
        ]);
        Jenis::create($data);
        return redirect('/jenis');
    }

    public function edit(Request $req)
    {
        $data = $req->validate([
            'nama' => 'required'
        ]);
        $jenis = Jenis::findOrFail($req->id);
        $jenis->nama = $data['nama'];
        $jenis->save();
        return redirect('/jenis');
    }

    public function delete($id)
    {
        $jenis = Jenis::where('id', $id)->first();
        $jenis->delete();
        return redirect('/jenis');
    }
}
