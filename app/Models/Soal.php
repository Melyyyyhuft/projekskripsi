<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ujians()
    {
        return $this->belongsToMany(Ujian::class, 'modul_ujian_soal', 'soal_id', 'ujian_id')->withTimestamps();
    }
}
