<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pendaftaran;

class PembayaranDaftarUlang extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_daftar_ulang';

    protected $guarded = ['id'];

    protected $casts = [
        'paid_at' => 'datetime',
        'jumlah'  => 'decimal:2',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}
