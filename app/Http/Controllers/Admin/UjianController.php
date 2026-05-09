<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Pendaftaran;

class UjianController extends Controller
{
    public function index()
    {
        $ujians = Ujian::latest()->get();
        return view('admin.ujian.index', compact('ujians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'         => 'required|string',
            'durasi_menit'  => 'required|integer',
            'jadwal_mulai'  => 'nullable|date',
            'jadwal_selesai'=> 'nullable|date|after_or_equal:jadwal_mulai',
        ]);

        Ujian::create([
            'judul'          => $request->judul,
            'durasi_menit'   => $request->durasi_menit,
            'jadwal_mulai'   => $request->jadwal_mulai,
            'jadwal_selesai' => $request->jadwal_selesai,
            'is_active'      => true,
            'is_tutup'       => false,
        ]);

        return back()->with('success', 'Ujian berhasil dibuat!');
    }

    public function show(Ujian $ujian)
    {
        $soals = Soal::where('ujian_id', $ujian->id)->get();

        // Ambil daftar peserta ujian untuk info admin
        $peserta = Pendaftaran::with(['user', 'jurusan'])
            ->whereIn('status', ['lolos_admin', 'sudah_ujian', 'tidak_mengikuti_ujian', 'siap_finalisasi', 'siap_diumumkan'])
            ->get();

        return view('admin.ujian.show', compact('ujian', 'soals', 'peserta'));
    }

    public function storeSoal(Request $request, Ujian $ujian)
    {
        $request->validate([
            'teks_soal'     => 'required|string',
            'opsi_a'        => 'required|string',
            'opsi_b'        => 'required|string',
            'opsi_c'        => 'required|string',
            'opsi_d'        => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        Soal::create(array_merge($request->all(), ['ujian_id' => $ujian->id]));
        return back()->with('success', 'Soal berhasil ditambahkan!');
    }

    /**
     * Menutup ujian: siswa yang belum ujian diberi status "tidak_mengikuti_ujian".
     */
    public function tutupUjian(Ujian $ujian)
    {
        // Tandai ujian sebagai tutup
        $ujian->update(['is_tutup' => true, 'is_active' => false]);

        // Siswa lolos_admin yang belum ujian → tidak_mengikuti_ujian
        $belumUjian = Pendaftaran::where('status', 'lolos_admin')->get();
        foreach ($belumUjian as $p) {
            $p->update(['status' => 'tidak_mengikuti_ujian']);
        }

        $jumlah = $belumUjian->count();

        return redirect()->route('admin.ujian.show', $ujian->id)
            ->with('success', "Ujian telah ditutup. {$jumlah} siswa yang belum ujian otomatis berstatus 'Tidak Mengikuti Ujian'.");
    }
}
