<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto.image'    => 'File harus berupa gambar.',
            'foto.mimes'    => 'Format foto harus JPG, PNG, atau WebP.',
            'foto.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        // Simpan foto baru
        $path = $request->file('foto')->store('foto_profil', 'public');

        $user->update(['foto' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
