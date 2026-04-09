<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use App\Models\HasilSeleksi;

class HasilController extends Controller
{
    public function index()
    {
        $pendaftaran = Pendaftaran::with('jurusan')
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$pendaftaran) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Anda belum melakukan pendaftaran.');
        }

        $hasil = HasilSeleksi::where('pendaftaran_id', $pendaftaran->id)->first();
        return view('siswa.hasil', compact('pendaftaran', 'hasil'));
    }
}
