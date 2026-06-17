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
            'tahun_ajaran' => 'required|string|max:50',
            'tgl_buka'     => 'required|date',
            'tgl_tutup'    => 'required|date|after_or_equal:tgl_buka',
            'status_ppdb'  => 'required|in:buka,tutup',
        ]);

        // Forced school name
        Pengaturan::updateOrCreate(['key' => 'nama_sekolah'], ['value' => 'SMK MITRA BINTARO']);
        
        Pengaturan::updateOrCreate(['key' => 'tahun_ajaran'], ['value' => $request->tahun_ajaran]);
        Pengaturan::updateOrCreate(['key' => 'status_ppdb'], ['value' => $request->status_ppdb]);
        Pengaturan::updateOrCreate(['key' => 'tgl_buka'], ['value' => $request->tgl_buka]);
        Pengaturan::updateOrCreate(['key' => 'tgl_tutup'], ['value' => $request->tgl_tutup]);

        return back()->with('success', 'Pengaturan Umum berhasil diperbarui!');
    }

    public function updatePeriode(Request $request)
    {
        $request->validate([
            'tgl_mulai_cbt' => 'required|date',
            'durasi_cbt' => 'required|numeric|min:1',
        ]);

        Pengaturan::updateOrCreate(['key' => 'tgl_mulai_cbt'], ['value' => $request->tgl_mulai_cbt]);
        Pengaturan::updateOrCreate(['key' => 'durasi_cbt'], ['value' => $request->durasi_cbt]);

        return back()->with('success', 'Pengaturan Jadwal CBT berhasil diperbarui!');
    }

    public function updateBobot(Request $request)
    {
        $request->validate([
            'bobot_ujian'     => 'required|numeric|min:0|max:100',
            'bobot_rapor'     => 'required|numeric|min:0|max:100',
            'ambang_unggulan' => 'required|numeric|min:0|max:100',
        ]);

        if ($request->bobot_ujian + $request->bobot_rapor != 100) {
            return back()->with('error', 'Total bobot ujian dan rapor harus 100%.');
        }

        Pengaturan::updateOrCreate(['key' => 'bobot_ujian'], ['value' => $request->bobot_ujian]);
        Pengaturan::updateOrCreate(['key' => 'bobot_rapor'], ['value' => $request->bobot_rapor]);
        Pengaturan::updateOrCreate(['key' => 'ambang_unggulan'], ['value' => $request->ambang_unggulan]);

        return back()->with('success', 'Pengaturan Bobot & Ambang Batas berhasil diperbarui!');
    }


}
