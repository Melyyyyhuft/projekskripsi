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
        $hasilUjian = HasilUjian::where('user_id', $this->user_id)->first();
        $nilaiCBT   = $hasilUjian ? (float) $hasilUjian->skor : null;
        $nilaiRapor = (float) $this->nilai_rapor;

        // Bonus: Disabled (User requested to remove bonus)
        $bonusVal = 0;

        // Formula logic
        $skorAkhir   = 0;
        $kategori    = 'TIDAK DITERIMA';
        $penempatan  = null;
        $hasCBT      = $nilaiCBT !== null;

        if ($hasCBT) {
            $raporPart = round(0.7 * $nilaiRapor, 2);
            $cbtPart   = round(0.3 * $nilaiCBT, 2);
            $skorAkhir = round($raporPart + $cbtPart, 2);

            $kategori   = 'DITERIMA';
            $penempatan = $skorAkhir >= 70 ? 'Kelas Unggulan' : 'Kelas Reguler';
        } else {
            $kategori = 'TIDAK HADIR CBT';
        }

        // Sync with HasilSeleksi
        $hs = $this->hasilSeleksi;
        
        // If it's a manual override, we might not want to overwrite it completely, 
        // but the user asked for "automatic fill" which implies keeping it updated.
        // We will update the 'system' fields regardless.
        $data = [
            'skor_sistem'        => $skorAkhir,
            'bonus_sistem'       => $bonusVal,
            'penempatan_sistem'  => $penempatan,
            'kategori_sistem'    => $kategori,
        ];

        // Only update the final result if it's NOT a manual override or if results don't exist yet
        if (!$hs || !$hs->is_manual_override) {
            $data['skor_akhir']         = $skorAkhir;
            $data['bonus_sertifikat']   = $bonusVal;
            $data['penempatan_kelas']   = $penempatan;
            $data['kategori_kelulusan'] = $kategori;
            $data['status_kelulusan']   = $kategori === 'DITERIMA';
        }

        if (!$hs) {
            $data['is_finalisasi'] = false;
            $data['status_proses'] = 'Sudah Dihitung';
            $data['ranking'] = 0;
        }

        return HasilSeleksi::updateOrCreate(
            ['pendaftaran_id' => $this->id],
            $data
        );
    }
}
