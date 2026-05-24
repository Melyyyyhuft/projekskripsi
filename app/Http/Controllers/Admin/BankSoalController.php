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
        // Ambil tahun ajaran & nama paket unik dari database untuk dropdown filter
        $tahunAjarans = Soal::select('tahun_ajaran')->distinct()->pluck('tahun_ajaran');
        $namaPakets = Soal::select('nama_paket')->distinct()->pluck('nama_paket');
        
        $filterTahun = $request->tahun_ajaran;
        $filterPaket = $request->nama_paket;
        
        if (!$filterTahun && !$filterPaket && $tahunAjarans->isNotEmpty()) {
            // Default ambil tahun ajaran pertama atau yang aktif
            $filterTahun = Pengaturan::where('key', 'tahun_ajaran_aktif')->first()->value ?? '2024/2025';
        }

        $soals = Soal::when($filterTahun, function ($q) use ($filterTahun) {
            return $q->where('tahun_ajaran', $filterTahun);
        })->when($filterPaket, function ($q) use ($filterPaket) {
            return $q->where('nama_paket', $filterPaket);
        })->get();

        return view('admin.bank_soal.index', compact('soals', 'tahunAjarans', 'namaPakets', 'filterTahun', 'filterPaket'));
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

    public function downloadTemplateExcel()
    {
        require_once app_path('Libraries/SimpleXLSXGen.php');
        
        $data = [
            ['Tahun Ajaran', 'Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Jawaban Benar'],
            ['2024/2025', 'Berapakah 1+1?', '1', '2', '3', '4', 'B'],
            ['2024/2025', 'Siapakah penemu gaya gravitasi?', 'Isaac Newton', 'Albert Einstein', 'Nikola Tesla', 'Thomas Edison', 'A']
        ];
        
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
        return response()->streamDownload(function() use ($xlsx) {
            echo $xlsx;
        }, 'template_bank_soal.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_soal' => 'required|file|max:10240', // max 10MB
        ]);

        $file = $request->file('file_soal');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->getRealPath();
        
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file.');
        }

        // Baca header
        $headerLine = fgets($handle);
        if (!$headerLine) {
            fclose($handle);
            return back()->with('error', 'File kosong.');
        }

        // Deteksi delimiter
        $delimiters = ["|", ";", ","];
        $delimiter = "|"; // Default
        $maxCount = 0;
        foreach ($delimiters as $d) {
            $count = substr_count($headerLine, $d);
            if ($count > $maxCount) {
                $maxCount = $count;
                $delimiter = $d;
            }
        }

        rewind($handle);
        $headerData = fgetcsv($handle, 0, $delimiter);
        
        // Validasi Kolom Header Secara Ketat
        $requiredHeaders = ['Tahun Ajaran', 'Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Jawaban Benar'];
        $actualHeaders = array_map('trim', $headerData);

        // Cari index kolom
        $colMap = [];
        foreach ($requiredHeaders as $req) {
            $found = false;
            foreach ($actualHeaders as $idx => $act) {
                if (strcasecmp($act, $req) === 0 || str_contains(strtolower($act), strtolower($req))) {
                    $colMap[$req] = $idx;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                fclose($handle);
                return back()->with('error', "Format file tidak sesuai! Kolom [$req] tidak ditemukan. Silakan gunakan template yang benar.");
            }
        }

        $validSoal = [];
        $errorCount = 0;
        $rowCount = 0;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
            $rowCount++;
            if (empty(array_filter($data))) continue;

            try {
                $tahun    = trim($data[$colMap['Tahun Ajaran']] ?? '');
                $soal     = trim($data[$colMap['Pertanyaan']] ?? '');
                $oa       = trim($data[$colMap['Opsi A']] ?? '');
                $ob       = trim($data[$colMap['Opsi B']] ?? '');
                $oc       = trim($data[$colMap['Opsi C']] ?? '');
                $od       = trim($data[$colMap['Opsi D']] ?? '');
                $jawaban  = strtoupper(trim($data[$colMap['Jawaban Benar']] ?? ''));

                if ($tahun && $soal && $oa && $ob && $oc && $od && in_array($jawaban, ['A', 'B', 'C', 'D'])) {
                    $validSoal[] = [
                        'tahun_ajaran' => $tahun,
                        'nama_paket'   => $fileName,
                        'teks_soal'    => $soal,
                        'opsi_a'       => $oa,
                        'opsi_b'       => $ob,
                        'opsi_c'       => $oc,
                        'opsi_d'       => $od,
                        'jawaban_benar' => $jawaban,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
            }
        }
        fclose($handle);

        if (count($validSoal) == 0) {
            return back()->with('error', 'Tidak ada soal valid yang ditemukan dalam file. Periksa format data dan kunci jawaban (A,B,C,D).');
        }

        // Chunk insert if many
        foreach (array_chunk($validSoal, 100) as $chunk) {
            Soal::insert($chunk);
        }
        
        $msg = count($validSoal) . ' soal berhasil diimport ke paket "' . $fileName . '".';
        if ($errorCount > 0) $msg .= " ({$errorCount} baris bermasalah diabaikan).";

        return back()->with('success', $msg);
    }
}
