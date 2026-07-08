<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_ajaran', 
        'nama_paket',
        'mapel',
        'gambar',
        'teks_soal', 
        'penjelasan',
        'opsi_a', 
        'opsi_b', 
        'opsi_c', 
        'opsi_d', 
        'jawaban_benar',
        'status'
    ];

    public function ujians()
    {
        return $this->belongsToMany(Ujian::class, 'modul_ujian_soal', 'soal_id', 'ujian_id')->withTimestamps();
    }
}
