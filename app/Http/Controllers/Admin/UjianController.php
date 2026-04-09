<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ujian;
use App\Models\Soal;

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
            'judul' => 'required|string',
            'durasi_menit' => 'required|integer',
        ]);
        
        Ujian::create([
            'judul' => $request->judul,
            'durasi_menit' => $request->durasi_menit,
            'is_active' => true
        ]);
        
        return back()->with('success', 'Ujian Berhasil Dibuat!');
    }

    public function show(Ujian $ujian)
    {
        $soals = Soal::where('ujian_id', $ujian->id)->get();
        return view('admin.ujian.show', compact('ujian', 'soals'));
    }

    public function storeSoal(Request $request, Ujian $ujian)
    {
        $request->validate([
            'teks_soal' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        Soal::create(array_merge($request->all(), ['ujian_id' => $ujian->id]));
        return back()->with('success', 'Soal berhasil ditambahkan!');
    }
}
