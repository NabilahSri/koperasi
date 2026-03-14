<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    public function index()
    {
        // echo "sadad";
        $data['pengaturan'] = Lembaga::all();
        return view('pages.login', $data);
    }

    public function show()
    {
        $user = Auth::user()->id;
        $data['user'] = User::where('id', $user)->first();
        return view('pages.profil', $data);
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'alamat' => ['nullable', 'string', 'max:255'],
            'nohp' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', Password::defaults()],
            'foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'alamat' => $validated['alamat'] ?? null,
            'nohp' => $validated['nohp'] ?? null,
        ];

        if (!empty($validated['password'] ?? null)) {
            $userData['password'] = bcrypt($validated['password']);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::delete($user->foto);
            }
            $ext = $request->file('foto')->getClientOriginalExtension();
            $fileName = $user->id . '_' . time() . '.' . $ext;
            $userData['foto'] = $request->file('foto')->storeAs('foto_users', $fileName);
        }

        $user->fill($userData);
        $user->save();

        return redirect('/profile');
    }

    public function auth(Request $request)
    {
        $credential = $request->only('no_user', 'password');
        if (Auth::attempt($credential)) {
            return redirect('/dashboard');
        } else {
            return redirect()->back();
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
