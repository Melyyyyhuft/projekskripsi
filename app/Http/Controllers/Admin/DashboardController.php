<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pendaftaran;
use App\Models\Jurusan;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPendaftar = Pendaftaran::count();
        $menungguVerifikasi = Pendaftaran::where('status', 'menunggu_verifikasi')->count();
        $totalDiterima = Pendaftaran::where('status', 'diterima')->count();
        
        $pendaftarTerbaru = Pendaftaran::with(['user', 'jurusan'])->latest()->take(5)->get();
        
        // Data statistik kuota jurusan
        $jurusans = Jurusan::all();

        return view('admin.dashboard', compact('totalPendaftar', 'menungguVerifikasi', 'totalDiterima', 'pendaftarTerbaru', 'jurusans'));
    }

    public function checkNewPendaftar()
    {
        $count = \Illuminate\Support\Facades\Auth::user()->unreadNotifications->count();
        return response()->json(['count' => $count]);
    }
}
