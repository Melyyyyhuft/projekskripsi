<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\HasilUjian;
use App\Models\HasilSeleksi;
use App\Models\Berkas;

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

    public function hasilUjian() {
        return $this->hasOne(HasilUjian::class, 'user_id', 'user_id');
    }

    /**
     * Calculate and sync selection result for this registration.
     */
    public function calculateSelectionResult()
    {
        $settings   = Pengaturan::pluck('value', 'key')->all();
        $bobotRapor = (float) ($settings['bobot_rapor'] ?? 70) / 100;
        $bobotUjian = (float) ($settings['bobot_ujian'] ?? 30) / 100;

        $hasilUjian = HasilUjian::where('user_id', $this->user_id)->first();
        $nilaiCBT   = $hasilUjian ? (float) $hasilUjian->skor : null;
        $nilaiRapor = (float) $this->nilai_rapor;

        // Bonus: Disabled (User requested to remove bonus)
        $bonusVal = 0;

        // Formula logic
        $skorAkhir   = 0;
        $kategori    = 'TIDAK DITERIMA';
        $penempatan  = '-';
        $hasCBT      = $nilaiCBT !== null;

        if ($hasCBT) {
            $raporPart = round($bobotRapor * $nilaiRapor, 2);
            $cbtPart   = round($bobotUjian * $nilaiCBT, 2);
            $skorAkhir = round($raporPart + $cbtPart, 2);

            $kategori   = $skorAkhir >= 60 ? 'DITERIMA' : 'TIDAK DITERIMA';
            $penempatan = '-'; // Placement logic removed by user request
        } else {
            $kategori = 'TIDAK HADIR CBT';
        }

        // Sync with HasilSeleksi
        $hs = $this->hasilSeleksi;
        
        $data = [
            'skor_sistem'        => $skorAkhir,
            'kategori_sistem'    => $kategori,
        ];

        // Only update the final result if it's NOT a manual override or if results don't exist yet
        if (!$hs || !$hs->is_manual_override) {
            $data['skor_akhir']         = $skorAkhir;
            $data['kategori_kelulusan'] = $kategori;
            $data['status_kelulusan']   = $kategori === 'DITERIMA';
        }

        if (!$hs) {
            $data['is_finalisasi'] = false;
            $data['status_proses'] = 'Sudah Dihitung';
        }

        return HasilSeleksi::updateOrCreate(
            ['pendaftaran_id' => $this->id],
            $data
        );
    }
}
