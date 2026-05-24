<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'jadwal_mulai' => 'datetime',
        'jadwal_selesai' => 'datetime',
        'is_active' => 'boolean',
        'is_tutup' => 'boolean',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function soals()
    {
        return $this->belongsToMany(Soal::class, 'modul_ujian_soal', 'ujian_id', 'soal_id')->withTimestamps();
    }
}
