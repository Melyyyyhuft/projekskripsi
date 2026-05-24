<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function ujian()
    {
        return $this->hasOne(Ujian::class);
    }

    // Hitung jumlah pendaftar (semua pendaftar aktif)
    public function getPendaftarCountAttribute()
    {
        // Menghitung semua pendaftar kecuali yang ditolak/dibatalkan jika ada
        return $this->pendaftarans()->whereNotIn('status', ['ditolak_admin', 'tidak_diterima'])->count();
    }

    // Hitung sisa kuota: kuota - jumlah pendaftar
    public function getSisaKuotaAttribute()
    {
        return max(0, $this->kuota - $this->pendaftar_count);
    }
}
