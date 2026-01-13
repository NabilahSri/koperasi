<?php

namespace App\Http\Controllers;

use App\Models\Lembaga;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
