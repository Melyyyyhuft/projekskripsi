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
        $settings = Pengaturan::pluck('value', 'key')->toArray();

        return view('siswa.hasil', compact('pendaftaran', 'hasil', 'hasilUjian', 'settings'));
    }

    /**
     * Download Surat Hasil PDF
     */
    public function downloadSurat()
    {
        $pendaftaran = Pendaftaran::with(['user', 'jurusan'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $hasil = HasilSeleksi::where('pendaftaran_id', $pendaftaran->id)
            ->where('is_finalisasi', true)
            ->firstOrFail();

        if ($hasil->kategori_kelulusan !== 'DITERIMA') {
            return back()->with('error', 'Surat hasil hanya tersedia untuk siswa yang diterima.');
        }

        $settings = Pengaturan::pluck('value', 'key')->toArray();

        // Jika package belum terinstall di env agent, kita beri pesan error yang baik
        if (!class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            return back()->with('error', 'Fitur PDF tidak tersedia. Mohon hubungi administrator untuk menginstal dompdf.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('siswa.hasil_pdf', compact('pendaftaran', 'hasil', 'settings'));
        $output = $pdf->output();
        $fileName = 'Surat_Hasil_PPDB_' . ($pendaftaran->nomor_pendaftaran ?? 'Siswa') . '.pdf';

        return response()->make($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => strlen($output),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
