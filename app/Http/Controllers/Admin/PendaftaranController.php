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
        $search = $request->query('search');

        $query = Pendaftaran::with(['user', 'jurusan'])->latest();

        if ($tab == 'baru') {
            $query->whereIn('status', ['menunggu_verifikasi', 'revisi']);
        } else {
            $query->whereNotIn('status', ['menunggu_verifikasi', 'revisi']);
        }

        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('nisn', 'like', "%{$search}%")
                ->orWhere('asal_sekolah', 'like', "%{$search}%")
                ->orWhere('nilai_rapor', 'like', "%{$search}%")
                ->orWhereHas('jurusan', function($jq) use ($search) {
                    $jq->where('nama', 'like', "%{$search}%");
                });
            });
        }

        $pendaftarans = $query->get();

        return view('admin.pendaftaran.index', compact('pendaftarans', 'tab', 'filterStatus', 'search'));
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
        
        try {
            $pendaftaran->update(['status' => $request->status]);
            
            if ($request->status === 'lolos_admin') {
                $pendaftaran->calculateSelectionResult();
            }

            $msg = $request->status == 'revisi' ? 'Permintaan revisi telah dikirim ke siswa.' : 'Siswa telah diloloskan verifikasi administrasi.';
            return redirect()->route('admin.pendaftaran.index', ['tab' => 'baru'])->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
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

        // Automatically recalculate selection if it's a certificate
        if ($berkas->jenis_berkas === 'sertifikat') {
            $pendaftaran = \App\Models\Pendaftaran::find($berkas->pendaftaran_id);
            if ($pendaftaran) {
                $pendaftaran->calculateSelectionResult();
            }
        }
        
        return back()->with('success', 'Status berkas berhasil diperbarui.');
    }
}
