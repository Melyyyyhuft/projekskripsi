<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        $statusPPDB = \App\Models\Pengaturan::where('key', 'status_ppdb')->value('value') ?? 'tutup';
        return view('auth.login', compact('statusPPDB'));
    }

    public function showRegister()
    {
        $statusPPDB = \App\Models\Pengaturan::where('key', 'status_ppdb')->first();
        if ($statusPPDB && $statusPPDB->value != 'buka') {
            return redirect('/login')->with('error', 'Pendaftaran PPDB saat ini sedang ditutup.');
        }
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if (Auth::user()->role == 'admin') {
                return redirect()->intended('/admin/dashboard');
            }
            return redirect()->intended('/siswa/dashboard');
        }

        return back()->with('error', 'Email atau Password salah.');
    }

    public function register(Request $request)
    {
        $statusPPDB = \App\Models\Pengaturan::where('key', 'status_ppdb')->first();
        if ($statusPPDB && $statusPPDB->value != 'buka') {
            return redirect('/login')->with('error', 'Gagal mendaftar! Pendaftaran PPDB telah ditutup.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa'
        ]);

        Auth::login($user);
        return redirect('/siswa/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
