<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use App\Models\Ujian;
use App\Models\HasilUjian;
use App\Models\HasilSeleksi;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Upload / update foto profil siswa (dari modal di layout).
     */
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $path = $request->file('foto')->store('foto-profil', 'public');
        $user->foto = $path;
        $user->save();

        return back()->with('success_foto', 'Foto profil berhasil diperbarui!');
    }

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
