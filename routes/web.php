<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\Jurusan;

Route::get('/', function () {
    $jurusans = \App\Models\Jurusan::all();
    $settings = \App\Models\Pengaturan::pluck('value', 'key')->all();
    return view('landing', compact('jurusans', 'settings'));
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Lupa Password (Statis)
Route::get('/lupa-password', function () {
    return view('auth.lupa-password');
});

// Admin Routes
Route::middleware(['auth', 'is.admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/api/cek-notifikasi', [\App\Http\Controllers\Admin\DashboardController::class, 'checkNewPendaftar']);
    
    // Notifikasi
    Route::get('/notifikasi', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifikasi.index');
    Route::get('/notifikasi/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifikasi.read');
    Route::post('/notifikasi/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifikasi.read_all');

    Route::get('/pendaftaran', [\App\Http\Controllers\Admin\PendaftaranController::class, 'index'])->name('admin.pendaftaran.index');
    Route::get('/pendaftaran/{id}', [\App\Http\Controllers\Admin\PendaftaranController::class, 'show'])->name('admin.pendaftaran.show');
    Route::post('/pendaftaran/{id}/verifikasi', [\App\Http\Controllers\Admin\PendaftaranController::class, 'verifikasi'])->name('admin.pendaftaran.verifikasi');
    Route::post('/berkas/{id}/verifikasi', [\App\Http\Controllers\Admin\PendaftaranController::class, 'verifikasiBerkas'])->name('admin.pendaftaran.verifikasi_berkas');
    // Rute Ujian & Seleksi
    Route::resource('ujian', \App\Http\Controllers\Admin\UjianController::class)->names('admin.ujian');
    Route::post('ujian/{ujian}/soal', [\App\Http\Controllers\Admin\UjianController::class, 'assignSoal'])->name('admin.ujian.soal.assign');
    Route::delete('ujian/{ujian}/soal/{soal}', [\App\Http\Controllers\Admin\UjianController::class, 'detachSoal'])->name('admin.ujian.soal.detach');
    Route::post('ujian/{ujian}/tutup', [\App\Http\Controllers\Admin\UjianController::class, 'tutupUjian'])->name('admin.ujian.tutup');

    
    Route::get('bank_soal/template', [\App\Http\Controllers\Admin\BankSoalController::class, 'downloadTemplate'])->name('admin.bank_soal.template');
    Route::get('bank_soal/template-excel', [\App\Http\Controllers\Admin\BankSoalController::class, 'downloadTemplateExcel'])->name('admin.bank_soal.template_excel');
    Route::post('bank_soal/import', [\App\Http\Controllers\Admin\BankSoalController::class, 'import'])->name('admin.bank_soal.import');
    Route::resource('bank_soal', \App\Http\Controllers\Admin\BankSoalController::class)->names('admin.bank_soal')->except(['show', 'edit', 'update']);
    
    Route::get('/seleksi', [\App\Http\Controllers\Admin\SeleksiController::class, 'index'])->name('admin.seleksi.index');
    Route::post('/seleksi/jalankan', [\App\Http\Controllers\Admin\SeleksiController::class, 'jalankanSeleksi'])->name('admin.seleksi.run');
    Route::post('/seleksi/tunda', [\App\Http\Controllers\Admin\SeleksiController::class, 'tundaSeleksi'])->name('admin.seleksi.tunda');
    Route::post('/seleksi/tanda-tidak-ujian', [\App\Http\Controllers\Admin\SeleksiController::class, 'tandaTidakIkutUjian'])->name('admin.seleksi.tanda-tidak-ujian');
    Route::post('/seleksi/finalisasi', [\App\Http\Controllers\Admin\SeleksiController::class, 'finalisasi'])->name('admin.seleksi.finalisasi');

    // Seleksi & Penempatan
    Route::get('/penempatan', [\App\Http\Controllers\Admin\PenempatanController::class, 'index'])->name('admin.penempatan.index');
    Route::post('/penempatan/proses', [\App\Http\Controllers\Admin\PenempatanController::class, 'prosesSeleksi'])->name('admin.penempatan.proses');
    Route::post('/penempatan/publish', [\App\Http\Controllers\Admin\PenempatanController::class, 'publishPengumuman'])->name('admin.penempatan.publish');

    
    // Pengaturan & Jurusan
    Route::get('/pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'index'])->name('admin.pengaturan.index');
    Route::post('/pengaturan/umum', [\App\Http\Controllers\Admin\PengaturanController::class, 'updateUmum'])->name('admin.pengaturan.umum');
    Route::post('/pengaturan/periode', [\App\Http\Controllers\Admin\PengaturanController::class, 'updatePeriode'])->name('admin.pengaturan.periode');
    Route::post('/pengaturan/bobot', [\App\Http\Controllers\Admin\PengaturanController::class, 'updateBobot'])->name('admin.pengaturan.bobot');
    
    Route::resource('jurusan-setting', \App\Http\Controllers\Admin\JurusanController::class)->names('admin.jurusan-setting');
});

// Siswa Routes
Route::middleware(['auth', 'is.siswa'])->prefix('siswa')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Siswa\DashboardController::class, 'index'])->name('siswa.dashboard');
    Route::get('/pendaftaran', [\App\Http\Controllers\Siswa\PendaftaranController::class, 'create'])->name('siswa.pendaftaran');
    Route::post('/pendaftaran', [\App\Http\Controllers\Siswa\PendaftaranController::class, 'store'])->name('siswa.pendaftaran.store');
    Route::post('/berkas/reupload', [\App\Http\Controllers\Siswa\PendaftaranController::class, 'reuploadBerkas'])->name('siswa.pendaftaran.reupload');
    
    // Rute CBT
    Route::get('/ujian', [\App\Http\Controllers\Siswa\UjianController::class, 'index'])->name('siswa.ujian');
    Route::post('/ujian/mulai', [\App\Http\Controllers\Siswa\UjianController::class, 'mulai'])->name('siswa.ujian.mulai');
    Route::post('/ujian/submit', [\App\Http\Controllers\Siswa\UjianController::class, 'submit'])->name('siswa.ujian.submit');
    
    // Hasil
    Route::get('/hasil', [\App\Http\Controllers\Siswa\HasilController::class, 'index'])->name('siswa.hasil');

    // Profil
    Route::post('/profil/foto', [\App\Http\Controllers\Siswa\ProfilController::class, 'uploadFoto'])->name('siswa.profil.foto');
});

// Halaman Tentang Kami
Route::get('/tentang-kami', function () {
    return view('tentang-kami');
});

// Halaman Jurusan
Route::get('/jurusan', function () {
    return view('jurusan');
});

// PPDB - Daftar
Route::get('/ppdb/daftar', function () {
    return view('ppdb.daftar');
});

// PPDB - Ujian Online
Route::get('/ppdb/ujian-online', function () {
    return view('ppdb.ujian-online');
});

// PPDB - Hasil Seleksi
Route::get('/ppdb/hasil-seleksi', function () {
    return view('ppdb.hasil-seleksi');
});
