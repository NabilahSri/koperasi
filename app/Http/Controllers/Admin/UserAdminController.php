<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kategori;
use App\Models\TransaksiS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('no_user', 'asc');

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
        $req->validate([
            'no_user' => 'required',
            'name' => 'required',
            'alamat' => 'required',
            'role' => 'required|in:admin,anggota,operator',
            'is_active' => 'required|in:0,1',
            'email' => 'nullable|email|unique:users,email',
            'nohp' => 'nullable',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'ktp' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $emailInput = trim((string) $req->email);
        $email = $emailInput !== '' ? $emailInput : null;
        if ($email === null) {
            $base = 'user' . preg_replace('/[^0-9]/', '', (string) $req->no_user);
            if ($base === 'user') {
                $base = 'user';
            }
            $candidate = $base . '@example.invalid';
            $i = 1;
            while (User::where('email', $candidate)->exists()) {
                $candidate = $base . '-' . $i . '@example.invalid';
                $i++;
            }
            $email = $candidate;
        }

        $nohpInput = trim((string) $req->nohp);
        $nohp = $nohpInput !== '' ? $nohpInput : '-';

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
            'email' => $email,
            'alamat' => $req->alamat,
            'nohp' => $nohp,
            'password' => bcrypt('12341234'),
            'foto' => $photoPath,
            'ktp' => $photoKTP,
            'role' => $req->role,
            'is_active' => (int) $req->is_active === 1,
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
        $req->validate([
            'no_user' => 'required',
            'name' => 'required',
            'alamat' => 'required',
            'role' => 'required|in:admin,anggota,operator',
            'is_active' => 'required|in:0,1',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'nohp' => 'nullable',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'ktp' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $userData = [
            'no_user' => $req->no_user,
            'name' => $req->name,
            'alamat' => $req->alamat,
            'role' => $req->role,
            'is_active' => (int) $req->is_active === 1,
        ];
        $emailInput = trim((string) $req->email);
        if ($emailInput !== '') {
            $userData['email'] = $emailInput;
        }
        $nohpInput = trim((string) $req->nohp);
        if ($nohpInput !== '') {
            $userData['nohp'] = $nohpInput;
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
