<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class UserAdminController extends Controller
{
    //
    public function index()
    {
        $data['admin'] = User::where('role', 'admin')->get();
        return view('pages.admin', $data);
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

        User::create([
            'no_user' => $req->no_user,
            'name' => $req->name,
            'email' => $req->email,
            'alamat' => $req->alamat,
            'nohp' => $req->nohp,
            'password' => bcrypt($req->password),
            'foto' => $photoPath,
            'ktp' => $photoKTP,
            'iuran_wajib' => $req->iuran_wajib,
            'role' => 'admin'
        ]);

        return redirect('/users/admin');
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
            'iuran_wajib' => $req->iuran_wajib,
            'iuran_pokok' => $req->iuran_pokok,
            'role' => 'admin'
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

        // return redirect('/users/admin');
    }

    public function delete(Request $req)
    {
        $user = User::findOrFail($req->id);
        $deleted = $user->delete();

        if ($deleted) {
            if ($user->foto) {
                Storage::delete('foto_users/' . $user->foto);
            }

            if ($user->ktp) {
                Storage::delete('foto_ktp/' . $user->ktp);
            }
        }

        return redirect('/users/admin');
    }
}
