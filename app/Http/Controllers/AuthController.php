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
        $isPPDBOpen = \App\Models\Pengaturan::isOpen();
        return view('auth.login', compact('isPPDBOpen'));
    }

    public function showRegister()
    {
        if (!\App\Models\Pengaturan::isOpen()) {
            return redirect('/login')->with('error', 'Mohon maaf, pendaftaran PPDB saat ini sedang ditutup.');
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
        if (!\App\Models\Pengaturan::isOpen()) {
            return redirect('/login')->with('error', 'Gagal mendaftar! Periode pendaftaran PPDB telah ditutup.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'unique:users,email', 'regex:/^[a-zA-Z][a-zA-Z0-9._]*@gmail\.com$/'],
            'password' => 'required|min:8|confirmed'
        ], [
            'name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'email.regex' => 'Email harus diawali dengan huruf dan wajib menggunakan domain @gmail.com.'
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
