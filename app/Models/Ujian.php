<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function soals()
    {
        return $this->belongsToMany(Soal::class, 'modul_ujian_soal', 'ujian_id', 'soal_id')->withTimestamps();
    }
}
