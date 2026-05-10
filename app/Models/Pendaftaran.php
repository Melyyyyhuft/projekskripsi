<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Jurusan;

class Pendaftaran extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    protected $casts = [
        'ditunda_seleksi' => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function jurusan() {
        return $this->belongsTo(Jurusan::class);
    }

    public function berkas() {
        return $this->hasMany(Berkas::class);
    }

    public function hasilSeleksi() {
        return $this->hasOne(HasilSeleksi::class);
    }
}
