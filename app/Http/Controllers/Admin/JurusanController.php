<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
        ]);

        Jurusan::create($request->all());

        return back()->with('success', 'Jurusan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'kuota' => 'required|integer|min:1',
        ]);

        $jurusan->update($request->all());

        return back()->with('success', 'Jurusan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        // Pastikan tidak ada pendaftar di jurusan ini sebelum menghapus
        if ($jurusan->pendaftarans()->count() > 0) {
            return back()->with('error', 'Gagal menghapus! Ada pendaftar di jurusan ini.');
        }

        $jurusan->delete();

        return back()->with('success', 'Jurusan berhasil dihapus!');
    }
}
