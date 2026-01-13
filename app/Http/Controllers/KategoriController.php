<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(){
        $data['kategori'] = Kategori::with('jenis')->get();
        $data['jenis'] = Jenis::all();
        return view('pages.kategori',$data);
    }

    public function create(Request $req){
        $data = $req->validate([
            'nama' => 'required',
            'id_jenis' => 'required'
        ]);
        Kategori::create($data);
        return redirect('/kategori');
    }

    public function edit(Request $req){
        $req->validate([
            'id_jenis' => 'required',
            'nama' => 'required',
        ]);
        $kategori = Kategori::findOrFail($req->id);
        $kategori->nama = $req->nama;
        $kategori->id_jenis = $req->id_jenis;
        $kategori->save();
        return redirect('/kategori');
    }

    public function delete($id){
        $kategori = Kategori::where('id',$id)->first();
        $kategori->delete();
        return redirect('/kategori');
    }
}
