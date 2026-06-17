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
            $filterTahun = Pengaturan::where('key', 'tahun_ajaran_aktif')->first()->value ?? '2024/2025';
        }

        $query = Soal::query();

        if ($filterTahun) $query->where('tahun_ajaran', $filterTahun);
        if ($filterPaket) $query->where('nama_paket', $filterPaket);
        
        if ($request->search) {
            $query->where('teks_soal', 'like', '%' . $request->search . '%');
        }
        if ($request->mapel) {
            $query->where('mapel', $request->mapel);
        }
        if ($request->status) {
            if ($request->status === 'Digunakan') {
                $query->whereIn('id', \DB::table('modul_ujian_soal')->pluck('soal_id'));
            } else {
                $query->where('status', $request->status);
            }
        }

        // Get all items for scrolling
        $soals = $query->latest()->get();

        // Statistics
        $stats = [
            'total' => Soal::count(),
            'aktif' => Soal::where('status', 'Aktif')->count(),
            'draft' => Soal::where('status', 'Draft')->count(),
            'digunakan' => \DB::table('modul_ujian_soal')->distinct('soal_id')->count()
        ];

        return view('admin.bank_soal.index', compact('soals', 'tahunAjarans', 'namaPakets', 'filterTahun', 'filterPaket', 'stats'));
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
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['gambar']);
        $data['sumber'] = 'Input Manual';

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('bank_soal', 'public');
            $data['gambar'] = $path;
        }

        Soal::updateOrCreate(['id' => $request->id], $data);

        return back()->with('success', 'Soal berhasil disimpan!');
    }

    public function destroy($id)
    {
        $soal = Soal::findOrFail($id);
        $soal->delete();
        return back()->with('success', 'Soal berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $soal = Soal::findOrFail($id);
        $soal->status = $soal->status === 'Aktif' ? 'Draft' : 'Aktif';
        $soal->save();

        return response()->json([
            'success' => true,
            'new_status' => $soal->status,
        ]);
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
        $output = (string) $xlsx;
        
        return response()->make($output, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_bank_soal.xlsx"',
            'Content-Length' => strlen($output),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_soal' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        $file = $request->file('file_soal');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->getRealPath();
        
        $validSoal = [];
        $errorCount = 0;

        if ($file->getClientOriginalExtension() === 'csv') {
            $handle = fopen($filePath, 'r');
            // SKIP HEADER
            fgetcsv($handle, 0, "|"); // assuming | as template uses it mostly, but let's be smarter
            
            // Re-detect delimiter
            rewind($handle);
            $headerLine = fgets($handle);
            $delimiters = ["|", ";", ","];
            $delimiter = "|";
            $maxCount = 0;
            foreach ($delimiters as $d) {
                $count = substr_count($headerLine, $d);
                if ($count > $maxCount) { $maxCount = $count; $delimiter = $d; }
            }
            rewind($handle);
            fgetcsv($handle, 0, $delimiter); // skip header

            while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                if (count($data) < 7) continue;
                $validSoal[] = $this->mapRow($data, $fileName);
            }
            fclose($handle);
        } else {
            // Excel
            require_once app_path('Libraries/SimpleXLSX.php');
            if ($xlsx = \Shuchkin\SimpleXLSX::parse($filePath)) {
                $rows = $xlsx->rows();
                array_shift($rows); // skip header
                foreach ($rows as $row) {
                    if (count($row) < 7) continue;
                    $validSoal[] = $this->mapRow($row, $fileName);
                }
            } else {
                return back()->with('error', \Shuchkin\SimpleXLSX::parseError());
            }
        }

        if (count($validSoal) == 0) {
            return back()->with('error', 'Tidak ada soal valid yang ditemukan.');
        }

        foreach (array_chunk($validSoal, 100) as $chunk) {
            Soal::insert($chunk);
        }
        
        return back()->with('success', count($validSoal) . ' soal berhasil diimport.');
    }

    public function export(Request $request)
    {
        $format = $request->format ?? 'excel';
        $soals = Soal::all();
        
        $data = [
            ['Tahun Ajaran', 'Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Jawaban Benar', 'Sumber', 'Status']
        ];
        
        foreach ($soals as $s) {
            $data[] = [
                $s->tahun_ajaran,
                $s->teks_soal,
                $s->opsi_a,
                $s->opsi_b,
                $s->opsi_c,
                $s->opsi_d,
                $s->jawaban_benar,
                $s->sumber,
                $s->status
            ];
        }

        if ($format === 'csv') {
            $filename = "export_bank_soal_" . date('Ymd_His') . ".csv";
            $handle = fopen('php://memory', 'w');
            foreach ($data as $row) {
                fputcsv($handle, $row, "|");
            }
            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);
            
            return response()->make($content, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } else {
            require_once app_path('Libraries/SimpleXLSXGen.php');
            $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
            $output = (string) $xlsx;
            
            return response()->make($output, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="export_bank_soal_' . date('Ymd_His') . '.xlsx"',
            ]);
        }
    }

    private function mapRow($row, $sumber)
    {
        return [
            'tahun_ajaran' => $row[0] ?? '2024/2025',
            'nama_paket'   => 'Imported',
            'teks_soal'    => $row[1] ?? '',
            'opsi_a'       => $row[2] ?? '',
            'opsi_b'       => $row[3] ?? '',
            'opsi_c'       => $row[4] ?? '',
            'opsi_d'       => $row[5] ?? '',
            'jawaban_benar' => strtoupper(trim($row[6] ?? 'A')),
            'sumber'       => $sumber,
            'status'       => 'Aktif',
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
