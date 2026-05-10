<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pendaftaran;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'baru'); // 'baru' atau 'arsip'
        $filterStatus = $request->query('status');

        $query = Pendaftaran::with(['user', 'jurusan'])->latest();

        if ($tab == 'baru') {
            $query->whereIn('status', ['menunggu_verifikasi', 'revisi']);
        } else {
            $query->whereNotIn('status', ['menunggu_verifikasi', 'revisi']);
        }

        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        $pendaftarans = $query->get();

        return view('admin.pendaftaran.index', compact('pendaftarans', 'tab', 'filterStatus'));
    }

    public function show($id)
    {
        $pendaftaran = Pendaftaran::with(['user', 'jurusan', 'berkas'])->findOrFail($id);
        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function verifikasi(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);
        $request->validate(['status' => 'required|in:revisi,lolos_admin']);
        
        $pendaftaran->update(['status' => $request->status]);
        
        return redirect()->route('admin.pendaftaran.index', ['tab' => 'baru'])->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function verifikasiBerkas(Request $request, $id)
    {
        $berkas = \App\Models\Berkas::findOrFail($id);
        $request->validate([
            'status_verifikasi' => 'required|in:valid,tidak_valid',
            'catatan_admin' => 'nullable|string'
        ]);
        
        $berkas->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_admin' => $request->status_verifikasi == 'tidak_valid' ? $request->catatan_admin : null
        ]);
        
        return back()->with('success', 'Status berkas berhasil diperbarui.');
    }
}
