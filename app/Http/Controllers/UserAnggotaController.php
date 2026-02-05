<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TransaksiS;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class UserAnggotaController extends Controller
{
    //
    public function index()
    {
        $data['anggota'] = User::where('role', 'anggota')->orderBy('no_user', 'desc')->get();
        return view('pages.anggota', $data);
    }

    public function create(Request $req)
    {
        // $this->validate($req, [
        //     'no_user' => 'required',
        //     'name' => 'required',
        //     'email' => ['required', 'email', 'unique:users'],
        //     'alamat' => 'required',
        //     'nohp' => ['required', 'min:10'],
        //     'password' => ['required', Password::min(6)->mixedCase()],
        //     'foto' => 'required',
        //     'ktp' => 'required',
        //     'iuran_wajib' => 'numeric',
        // ]);

        if ($req->hasFile('foto')) {
            $photoPath = $req->file('foto')->storeAs('foto_users', $req->name . '.' . $req->file('foto')->getClientOriginalExtension());
        } else {
            $photoPath = null;
        }

        if ($req->hasFile('ktp')) {
            $photoKTP = $req->file('ktp')->storeAs('foto_ktp', $req->name . '.' . $req->file('ktp')->getClientOriginalExtension());
        } else {
            $photoKTP = null;
        }

        $user = User::create([
            'no_user' => $req->no_user,
            'name' => $req->name,
            'email' => $req->email,
            'alamat' => $req->alamat,
            'nohp' => $req->nohp,
            'password' => bcrypt($req->password),
            'foto' => $photoPath,
            'ktp' => $photoKTP,
            'role' => 'anggota'
        ]);
        if ($user) {
            $kategoriPokok = Kategori::where('nama', 'Iuran Pokok')->first();
            $kategoriWajib = Kategori::where('nama', 'Iuran Wajib')->first();

            if ($req->iuran_pokok && $kategoriPokok) {
                TransaksiS::create([
                    'id_user' => $user->id,
                    'id_kategori' => $kategoriPokok->id,
                    'id_petugas' => Auth::id(),
                    'nama_penyetor' => $user->name,
                    'jumlah' => $req->iuran_pokok,
                    'tanggal' => date('Y-m-d'),
                    'keterangan' => 'Iuran Pokok',
                ]);
            }

            if ($req->iuran_wajib && $kategoriWajib) {
                TransaksiS::create([
                    'id_user' => $user->id,
                    'id_kategori' => $kategoriWajib->id,
                    'id_petugas' => Auth::id(),
                    'nama_penyetor' => $user->name,
                    'jumlah' => $req->iuran_wajib,
                    'tanggal' => date('Y-m-d'),
                    'keterangan' => 'Iuran Wajib',
                ]);
            }
        }

        return redirect('/users/anggota');
    }

    public function edit(Request $req, $id)
    {
        $user = User::find($id);
        $userData = [
            'no_user' => $req->no_user,
            'name' => $req->name,
            'email' => $req->email,
            'alamat' => $req->alamat,
            'nohp' => $req->nohp,
            'role' => 'anggota'
        ];
        if (!empty($req->password)) {
            $userData['password'] = bcrypt($req->password);
        }

        if ($req->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                Storage::delete($user->foto);
            }

            // Simpan foto baru ke direktori penyimpanan yang sesuai
            $photoPath = $req->file('foto')->storeAs('foto_users', $req->name . '.' . $req->file('foto')->getClientOriginalExtension());

            // Update path foto di database
            $userData['foto'] = $photoPath;
        }

        if ($req->hasFile('ktp')) {
            // Hapus foto lama jika ada
            if ($user->ktp) {
                Storage::delete($user->ktp);
            }

            // Simpan foto baru ke direktori penyimpanan yang sesuai
            $photoKTP = $req->file('ktp')->storeAs('foto_ktp', $req->name . '.' . $req->file('ktp')->getClientOriginalExtension());

            // Update path foto di database
            $userData['ktp'] = $photoKTP;
        }

        $user->fill($userData);
        $user->save();

        return redirect('/users/anggota');
    }

    public function delete(Request $req)
    {
        $user = User::findOrFail($req->id);
        $user->delete();

        return redirect('/users/anggota');
    }
}
