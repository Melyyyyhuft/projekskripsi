<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use App\Models\HasilSeleksi;
use App\Models\HasilUjian;
use App\Models\Pengaturan;

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

        $hasil      = HasilSeleksi::where('pendaftaran_id', $pendaftaran->id)->first();
        $hasilUjian = HasilUjian::where('user_id', Auth::id())->first();

        // Ambil pengaturan sekolah
        $settingRows = Pengaturan::whereIn('key', ['nama_sekolah', 'logo_sekolah', 'tahun_ajaran', 'tgl_pengumuman'])
            ->pluck('value', 'key');
        $settings = $settingRows->toArray();

        return view('siswa.hasil', compact('pendaftaran', 'hasil', 'hasilUjian', 'settings'));
    }
}
