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

    // Hitung jumlah pendaftar yang sudah diterima
    public function getDiterimaCountAttribute()
    {
        return $this->pendaftarans()->where('status', 'diterima')->count();
    }

    // Hitung sisa kuota: kuota - jumlah diterima
    public function getSisaKuotaAttribute()
    {
        return max(0, $this->kuota - $this->diterima_count);
    }
}
