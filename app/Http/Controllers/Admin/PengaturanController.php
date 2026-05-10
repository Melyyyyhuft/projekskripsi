<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan;
use App\Models\Jurusan;

class PengaturanController extends Controller
{
    public function index()
    {
        $settings = Pengaturan::pluck('value', 'key')->all();
        $jurusans = Jurusan::withCount('pendaftarans')->get();
        return view('admin.pengaturan.index', compact('settings', 'jurusans'));
    }

    public function updateUmum(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:50',
            'status_ppdb' => 'required|in:buka,tutup',
            'logo_sekolah' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        Pengaturan::updateOrCreate(['key' => 'nama_sekolah'], ['value' => $request->nama_sekolah]);
        Pengaturan::updateOrCreate(['key' => 'tahun_ajaran'], ['value' => $request->tahun_ajaran]);
        Pengaturan::updateOrCreate(['key' => 'status_ppdb'], ['value' => $request->status_ppdb]);

        if ($request->hasFile('logo_sekolah')) {
            $file = $request->file('logo_sekolah');
            $path = $file->store('logo', 'public');
            Pengaturan::updateOrCreate(['key' => 'logo_sekolah'], ['value' => $path]);
        }

        return back()->with('success', 'Pengaturan Umum berhasil diperbarui!');
    }

    public function updatePeriode(Request $request)
    {
        $request->validate([
            'tgl_buka' => 'required|date',
            'tgl_tutup' => 'required|date|after_or_equal:tgl_buka',
            'tgl_mulai_cbt' => 'required|date',
            'durasi_cbt' => 'required|numeric|min:1',
        ]);

        Pengaturan::updateOrCreate(['key' => 'tgl_buka'], ['value' => $request->tgl_buka]);
        Pengaturan::updateOrCreate(['key' => 'tgl_tutup'], ['value' => $request->tgl_tutup]);
        Pengaturan::updateOrCreate(['key' => 'tgl_mulai_cbt'], ['value' => $request->tgl_mulai_cbt]);
        Pengaturan::updateOrCreate(['key' => 'durasi_cbt'], ['value' => $request->durasi_cbt]);

        // Cek dan update status PPDB secara otomatis
        $today = date('Y-m-d');
        if ($today > $request->tgl_tutup) {
            Pengaturan::updateOrCreate(['key' => 'status_ppdb'], ['value' => 'tutup']);
        }

        return back()->with('success', 'Pengaturan Periode Pendaftaran & CBT berhasil diperbarui!');
    }

    public function updateBobot(Request $request)
    {
        $request->validate([
            'bobot_ujian' => 'required|numeric|min:0|max:100',
            'bobot_rapor' => 'required|numeric|min:0|max:100',
        ]);

        if ($request->bobot_ujian + $request->bobot_rapor != 100) {
            return back()->with('error', 'Total bobot ujian dan rapor harus 100%.');
        }

        Pengaturan::updateOrCreate(['key' => 'bobot_ujian'], ['value' => $request->bobot_ujian]);
        Pengaturan::updateOrCreate(['key' => 'bobot_rapor'], ['value' => $request->bobot_rapor]);

        return back()->with('success', 'Pengaturan Bobot Nilai Seleksi berhasil diperbarui!');
    }
}
