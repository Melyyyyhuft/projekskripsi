<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pendaftaran;

class PendaftaranController extends Controller
{
    public function index()
    {
        $pendaftarans = Pendaftaran::with(['user', 'jurusan'])->latest()->get();
        return view('admin.pendaftaran.index', compact('pendaftarans'));
    }

    public function verifikasi(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);
        $request->validate(['status' => 'required|in:lolos_admin,ditolak_admin']);
        
        $pendaftaran->update(['status' => $request->status]);
        
        return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }
}
