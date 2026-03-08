<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kategori;
use App\Models\TransaksiS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('role', 'asc');

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        $data['users'] = $query->get();
        $data['currentRole'] = $request->role;

        $lastUser = User::orderByRaw('CAST(no_user AS UNSIGNED) DESC')->first();
        $data['nextNoUser'] = $lastUser ? str_pad((int)$lastUser->no_user + 1, 3, '0', STR_PAD_LEFT) : '001';

        return view('admin.pages.users', $data);
    }

    public function create(Request $req)
    {
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
            'role' => $req->role
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
                    'jumlah' => str_replace(['Rp ', '.'], '', $req->iuran_pokok),
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
                    'jumlah' => str_replace(['Rp ', '.'], '', $req->iuran_wajib),
                    'tanggal' => date('Y-m-d'),
                    'keterangan' => 'Iuran Wajib',
                ]);
            }
        }

        return redirect('/users');
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
            'role' => $req->role
        ];
        if (!empty($req->password)) {
            $userData['password'] = bcrypt($req->password);
        }

        if ($req->hasFile('foto')) {
            if ($user->foto) {
                Storage::delete($user->foto);
            }
            $photoPath = $req->file('foto')->storeAs('foto_users', $req->name . '.' . $req->file('foto')->getClientOriginalExtension());
            $userData['foto'] = $photoPath;
        }

        if ($req->hasFile('ktp')) {
            if ($user->ktp) {
                Storage::delete($user->ktp);
            }
            $photoKTP = $req->file('ktp')->storeAs('foto_ktp', $req->name . '.' . $req->file('ktp')->getClientOriginalExtension());
            $userData['ktp'] = $photoKTP;
        }

        $user->fill($userData);
        $user->save();

        return redirect('/users');
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

        return redirect('/users');
    }
}
