<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pendaftaran;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPendaftar = Pendaftaran::count();
        $menungguVerifikasi = Pendaftaran::where('status', 'menunggu_verifikasi')->count();
        $totalDiterima = Pendaftaran::where('status', 'diterima')->count();
        
        $pendaftarTerbaru = Pendaftaran::with(['user', 'jurusan'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalPendaftar', 'menungguVerifikasi', 'totalDiterima', 'pendaftarTerbaru'));
    }
}
