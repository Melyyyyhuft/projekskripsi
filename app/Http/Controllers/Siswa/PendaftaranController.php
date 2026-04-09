<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\Berkas;

class PendaftaranController extends Controller
{
    public function create()
    {
        $jurusans = Jurusan::all();
        $pendaftaran = Pendaftaran::where('user_id', Auth::id())->first();
        return view('siswa.pendaftaran', compact('jurusans', 'pendaftaran'));
    }

    public function store(Request $request)
    {
        // Validasi Relasional dan File Upload
        $request->validate([
            'jurusan_id' => 'required|exists:jurusans,id',
            'nisn' => 'required|numeric',
            'asal_sekolah' => 'required|string|max:255',
            'nilai_rapor' => 'required|numeric|min:0|max:100',
            'berkas.*' => 'required|mimes:pdf,jpg,jpeg,png|max:2048' // Validasi Format Berkas
        ]);

        $pendaftaran = Pendaftaran::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'jurusan_id' => $request->jurusan_id,
                'nisn' => $request->nisn,
                'asal_sekolah' => $request->asal_sekolah,
                'nilai_rapor' => $request->nilai_rapor,
                'status' => 'menunggu_verifikasi'
            ]
        );

        if($request->hasFile('berkas')) {
            foreach($request->file('berkas') as $file) {
                $path = $file->store('berkas_pendaftaran', 'public');
                Berkas::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension()
                ]);
            }
        }

        return redirect()->route('siswa.dashboard')->with('success', 'Pendaftaran berhasil disubmit dan menunggu verifikasi.');
    }
}
