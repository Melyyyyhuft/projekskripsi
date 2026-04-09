<?php

use Illuminate\Support\Facades\Route;

// Halaman utama → Beranda
Route::get('/', function () {
    return view('beranda');
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
