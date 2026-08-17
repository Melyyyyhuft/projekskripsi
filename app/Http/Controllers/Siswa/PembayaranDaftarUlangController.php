<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Pendaftaran;
use App\Models\PembayaranDaftarUlang;
use App\Models\Pengaturan;
use Midtrans\Config;
use Midtrans\Snap;

class PembayaranDaftarUlangController extends Controller
{
    /**
     * Menampilkan halaman Pembayaran Daftar Ulang
     * untuk siswa yang telah diterima.
     */
    public function index()
    {
        $user_id = Auth::id();

        $pendaftaran = Pendaftaran::with([
            'jurusan',
            'hasilSeleksi',
            'pembayaranDaftarUlang'
        ])
            ->where('user_id', $user_id)
            ->first();

        // Hanya siswa yang sudah diterima yang boleh mengakses
        if (!$pendaftaran || !$pendaftaran->isDiterima()) {
            return redirect()->route('siswa.dashboard')
                ->with(
                    'error',
                    'Fitur pembayaran daftar ulang hanya dapat diakses oleh calon siswa yang telah dinyatakan Diterima / Lulus seleksi.'
                );
        }

        // Ambil nominal biaya dari pengaturan
        $biayaSetting = Pengaturan::where(
            'key',
            'biaya_daftar_ulang'
        )->value('value');

        $biaya = (
            is_numeric($biayaSetting)
            && (float) $biayaSetting > 0
        )
            ? (float) $biayaSetting
            : null;

        // Ambil data pembayaran
        $pembayaran = $pendaftaran->pembayaranDaftarUlang;

        // Jika belum ada, buat data pembayaran
        if (!$pembayaran && $biaya !== null) {
            $pembayaran = PembayaranDaftarUlang::create([
                'pendaftaran_id' => $pendaftaran->id,

                // Order ID awal
                'order_id' => 'DU-' . date('Ymd') . '-' .
                    str_pad(
                        $pendaftaran->id,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),

                'jumlah' => $biaya,
                'status' => 'belum_bayar',
            ]);
        }

        // Update nominal jika pengaturan berubah
        elseif (
            $pembayaran
            && $pembayaran->status === 'belum_bayar'
            && $biaya !== null
            && (float) $pembayaran->jumlah !== (float) $biaya
        ) {
            $pembayaran->update([
                'jumlah' => $biaya
            ]);
        }

        $settings = Pengaturan::pluck('value', 'key')->all();

        return view(
            'siswa.pembayaran.index',
            compact(
                'pendaftaran',
                'pembayaran',
                'biaya',
                'settings'
            )
        );
    }


    /**
     * Membuat Snap Token Midtrans.
     */
    public function bayar(Request $request)
    {
        $user_id = Auth::id();

        $pendaftaran = Pendaftaran::with([
            'jurusan',
            'hasilSeleksi',
            'pembayaranDaftarUlang'
        ])
            ->where('user_id', $user_id)
            ->first();

        // Validasi siswa
        if (!$pendaftaran || !$pendaftaran->isDiterima()) {
            return response()->json([
                'message' =>
                    'Pembayaran hanya dapat dilakukan oleh calon siswa yang telah diterima.'
            ], 403);
        }

        // Ambil pembayaran
        $pembayaran = $pendaftaran->pembayaranDaftarUlang;

        if (!$pembayaran) {
            return response()->json([
                'message' =>
                    'Data pembayaran daftar ulang belum tersedia.'
            ], 404);
        }

        // Pastikan belum dibayar
        if ($pembayaran->status !== 'belum_bayar') {
            return response()->json([
                'message' =>
                    'Pembayaran ini sudah diproses.'
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | MIDTRANS CONFIG
        |--------------------------------------------------------------------------
        */

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;


        /*
        |--------------------------------------------------------------------------
        | BUAT ORDER ID BARU
        |--------------------------------------------------------------------------
        |
        | Setiap kali membuat transaksi Midtrans baru,
        | Order ID harus unik.
        |
        */

        $orderId =
            'DU-' .
            date('YmdHis') .
            '-' .
            $pendaftaran->id .
            '-' .
            strtoupper(Str::random(6));


        /*
        |--------------------------------------------------------------------------
        | Simpan Order ID baru
        |--------------------------------------------------------------------------
        */

        $pembayaran->update([
            'order_id' => $orderId
        ]);


        /*
        |--------------------------------------------------------------------------
        | PARAMETER MIDTRANS
        |--------------------------------------------------------------------------
        */

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $pembayaran->jumlah,
            ],

            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | REQUEST SNAP TOKEN
        |--------------------------------------------------------------------------
        */

        try {

            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Gagal membuat pembayaran.',
                'error' =>
                    $e->getMessage(),
            ], 500);
        }
    }
}