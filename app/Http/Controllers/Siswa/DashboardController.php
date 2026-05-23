<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use App\Models\Ujian;
use App\Models\HasilUjian;
use App\Models\HasilSeleksi;

class DashboardController extends Controller
{
    public function index()
    {
        $user_id     = Auth::id();
        $pendaftaran = Pendaftaran::with(['jurusan', 'hasilSeleksi', 'berkas'])
            ->where('user_id', $user_id)
            ->first();

        // Ujian aktif: belum ditutup dan masih aktif
        $ujian_aktif = Ujian::where('is_active', true)
            ->where('is_tutup', false)
            ->first();

        // Nilai ujian siswa (jika sudah mengerjakan)
        $hasilUjian = $pendaftaran
            ? HasilUjian::where('user_id', $user_id)->first()
            : null;

        // Hasil seleksi (draft atau final)
        $hasilSeleksi = $pendaftaran ? $pendaftaran->hasilSeleksi : null;

        $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();

        return view('siswa.dashboard', compact(
            'pendaftaran',
            'ujian_aktif',
            'hasilUjian',
            'hasilSeleksi',
            'settings'
        ));
    }
}
