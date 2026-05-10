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
            'acak_soal'     => 'nullable|boolean',
            'acak_jawaban'  => 'nullable|boolean',
        ]);

        Ujian::create([
            'judul'          => $request->judul,
            'durasi_menit'   => $request->durasi_menit,
            'jadwal_mulai'   => $request->jadwal_mulai,
            'jadwal_selesai' => $request->jadwal_selesai,
            'acak_soal'      => $request->has('acak_soal'),
            'acak_jawaban'   => $request->has('acak_jawaban'),
            'is_active'      => true,
            'is_tutup'       => false,
        ]);

        return back()->with('success', 'Ujian berhasil dibuat!');
    }

    public function show(Request $request, Ujian $ujian)
    {
        $soals = $ujian->soals;

        // Ambil soal-soal di Bank Soal yang belum ada di modul ini
        $bankSoalsQuery = Soal::whereNotIn('id', $soals->pluck('id'));
        
        // Filter tahun ajaran jika ada
        if ($request->filled('tahun_ajaran')) {
            $bankSoalsQuery->where('tahun_ajaran', $request->tahun_ajaran);
        }
        
        $bankSoals = $bankSoalsQuery->get();
        $tahunAjarans = Soal::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran');

        // Ambil daftar peserta ujian untuk info admin
        $peserta = Pendaftaran::with(['user', 'jurusan'])
            ->whereIn('status', ['lolos_admin', 'sudah_ujian', 'tidak_mengikuti_ujian', 'siap_finalisasi', 'siap_diumumkan'])
            ->get();

        return view('admin.ujian.show', compact('ujian', 'soals', 'bankSoals', 'tahunAjarans', 'peserta'));
    }

    public function assignSoal(Request $request, Ujian $ujian)
    {
        $request->validate([
            'soal_ids' => 'required|array',
            'soal_ids.*' => 'exists:soals,id'
        ]);

        // attach tanpa detaching yang sudah ada
        $ujian->soals()->syncWithoutDetaching($request->soal_ids);

        return back()->with('success', count($request->soal_ids) . ' soal berhasil ditambahkan ke Modul Ujian ini.');
    }

    public function detachSoal(Request $request, Ujian $ujian, Soal $soal)
    {
        $ujian->soals()->detach($soal->id);
        return back()->with('success', 'Soal berhasil dikeluarkan dari Modul Ujian.');
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
