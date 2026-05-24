<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Pendaftaran;
use App\Models\Jurusan;
use App\Models\Pengaturan;

class UjianController extends Controller
{
    public function index()
    {
        $ujians = Ujian::with('jurusan')->withCount('soals')->get();
        $jurusans = Jurusan::all();
        $settings = Pengaturan::pluck('value', 'key')->all();
        
        return view('admin.ujian.index', compact('ujians', 'jurusans', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'         => 'required|string',
            'jurusan_id'    => 'nullable|exists:jurusans,id',
            'durasi_menit'  => 'required|integer',
            'jadwal_mulai'  => 'nullable|date',
            'jadwal_selesai'=> 'nullable|date|after_or_equal:jadwal_mulai',
            'acak_soal'     => 'nullable|boolean',
            'acak_jawaban'  => 'nullable|boolean',
        ]);

        Ujian::create([
            'judul'          => $request->judul,
            'jurusan_id'     => $request->jurusan_id,
            'durasi_menit'   => $request->durasi_menit,
            'jadwal_mulai'   => $request->jadwal_mulai,
            'jadwal_selesai' => $request->jadwal_selesai,
            'acak_soal'      => $request->has('acak_soal'),
            'acak_jawaban'   => $request->has('acak_jawaban'),
            'is_active'      => true,
            'is_tutup'       => false,
        ]);

        return back()->with('success', 'Modul Ujian berhasil dibuat!');
    }

    public function saveCbtSettings(Request $request)
    {
        $request->validate([
            'cbt_tgl_mulai'   => 'required|date',
            'cbt_tgl_selesai' => 'required|date|after:cbt_tgl_mulai',
            'cbt_durasi_default' => 'required|integer|min:1',
            'cbt_max_percobaan'  => 'required|integer|min:1',
            'cbt_status'      => 'required|in:aktif,ditutup'
        ]);

        foreach ($request->only(['cbt_tgl_mulai', 'cbt_tgl_selesai', 'cbt_durasi_default', 'cbt_max_percobaan', 'cbt_status']) as $key => $value) {
            Pengaturan::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Pengaturan Global CBT berhasil diperbarui!');
    }

    public function toggleStatus(Ujian $ujian)
    {
        $ujian->update(['is_active' => !$ujian->is_active]);
        $status = $ujian->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Modul Ujian berhasil {$status}!");
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

        // Filter nama paket jika ada
        if ($request->filled('nama_paket')) {
            $bankSoalsQuery->where('nama_paket', $request->nama_paket);
        }
        
        $bankSoals = $bankSoalsQuery->get();
        $tahunAjarans = Soal::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran');
        $namaPakets = Soal::select('nama_paket')->distinct()->pluck('nama_paket');

        // Ambil daftar peserta ujian untuk info admin
        $peserta = Pendaftaran::with(['user', 'jurusan'])
            ->whereIn('status', ['lolos_admin', 'sudah_ujian', 'tidak_mengikuti_ujian', 'siap_finalisasi', 'siap_diumumkan'])
            ->get();

        return view('admin.ujian.show', compact('ujian', 'soals', 'bankSoals', 'tahunAjarans', 'namaPakets', 'peserta'));
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
     * Membuka kembali ujian yang sudah ditutup
     */
    public function bukaUjian(Ujian $ujian)
    {
        $ujian->update(['is_tutup' => false, 'is_active' => true]);
        return back()->with('success', 'Ujian berhasil dibuka kembali.');
    }

    /**
     * Memperpanjang waktu ujian (+1 hari)
     */
    public function perpanjangUjian(Ujian $ujian)
    {
        $newTime = $ujian->jadwal_selesai ? \Carbon\Carbon::parse($ujian->jadwal_selesai)->addDay() : now()->addDay();
        $ujian->update([
            'jadwal_selesai' => $newTime,
            'is_tutup' => false,
            'is_active' => true
        ]);

        return back()->with('success', 'Waktu ujian berhasil diperpanjang hingga ' . $newTime->format('d M Y H:i'));
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
            ->with('success', "Ujian telah ditutup secara manual. {$jumlah} siswa yang belum ujian otomatis berstatus 'Tidak Mengikuti Ujian'.");
    }
}
