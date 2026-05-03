<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Soal;
use App\Models\Pengaturan;

class BankSoalController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tahun ajaran unik dari database untuk dropdown filter
        $tahunAjarans = Soal::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran');
        
        $filterTahun = $request->tahun_ajaran;
        
        if (!$filterTahun && $tahunAjarans->isNotEmpty()) {
            // Default ambil tahun ajaran pertama atau yang aktif
            $filterTahun = Pengaturan::where('key', 'tahun_ajaran_aktif')->first()->value ?? '2024/2025';
        }

        $soals = Soal::when($filterTahun, function ($q) use ($filterTahun) {
            return $q->where('tahun_ajaran', $filterTahun);
        })->get();

        return view('admin.bank_soal.index', compact('soals', 'tahunAjarans', 'filterTahun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string',
            'teks_soal' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        Soal::create($request->all());

        return back()->with('success', 'Soal berhasil ditambahkan ke Bank Soal!');
    }

    public function destroy($id)
    {
        $soal = Soal::findOrFail($id);
        $soal->delete();
        return back()->with('success', 'Soal berhasil dihapus!');
    }

    public function downloadTemplate()
    {
        $content = "Tahun Ajaran|Pertanyaan|Opsi A|Opsi B|Opsi C|Opsi D|Jawaban Benar\n2024/2025|Berapakah 1+1?|1|2|3|4|B\n2024/2025|Siapakah penemu gaya gravitasi?|Isaac Newton|Albert Einstein|Nikola Tesla|Thomas Edison|A\n";
        $headers = [
            'Content-type' => 'text/plain', 
            'Content-Disposition' => sprintf('attachment; filename="%s"', 'template_bank_soal.txt')
        ];
        return response()->make($content, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_soal' => 'required|file|max:5120', // max 5MB
        ]);

        $file = $request->file('file_soal');
        $content = file_get_contents($file->getRealPath());
        $lines = explode("\n", str_replace("\r", "", $content));
        
        $validSoal = [];
        foreach ($lines as $index => $line) {
            if ($index == 0) continue; // Skip header
            if (empty(trim($line))) continue;

            $delimiter = '|';
            if (strpos($line, '|') === false) {
                if (strpos($line, ';') !== false) {
                    $delimiter = ';';
                } else {
                    $delimiter = ',';
                }
            }

            $data = str_getcsv($line, $delimiter);
            
            if (count($data) >= 7) {
                $jawaban = strtoupper(trim($data[6]));
                if (in_array($jawaban, ['A', 'B', 'C', 'D'])) {
                    $validSoal[] = [
                        'tahun_ajaran' => trim($data[0]),
                        'teks_soal' => trim($data[1]),
                        'opsi_a' => trim($data[2]),
                        'opsi_b' => trim($data[3]),
                        'opsi_c' => trim($data[4]),
                        'opsi_d' => trim($data[5]),
                        'jawaban_benar' => $jawaban,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (count($validSoal) < 50) {
            return back()->with('error', 'Gagal import! File hanya memiliki ' . count($validSoal) . ' soal valid (kurang dari 50). Pastikan formatnya benar.');
        }

        Soal::insert($validSoal);

        return back()->with('success', count($validSoal) . ' soal massal berhasil diimport ke Bank Soal!');
    }
}
