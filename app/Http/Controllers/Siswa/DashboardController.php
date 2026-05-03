<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use App\Models\Ujian;

class DashboardController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $pendaftaran = Pendaftaran::where('user_id', $user_id)->first();
        $ujian_aktif = Ujian::where('is_active', true)->first();

        return view('siswa.dashboard', compact('pendaftaran', 'ujian_aktif'));
    }
}
