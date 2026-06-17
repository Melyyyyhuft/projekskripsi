<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function isOpen()
    {
        $settings = self::pluck('value', 'key')->all();
        $status = $settings['status_ppdb'] ?? 'tutup';
        $tglBuka = $settings['tgl_buka'] ?? null;
        $tglTutup = $settings['tgl_tutup'] ?? null;
        $today = date('Y-m-d');

        if ($status !== 'buka') return false;
        if ($tglBuka && $today < $tglBuka) return false;
        if ($tglTutup && $today > $tglTutup) return false;

        return true;
    }
}
