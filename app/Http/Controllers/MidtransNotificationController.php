<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembayaranDaftarUlang;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransNotificationController extends Controller
{
    public function handle(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;

            $pembayaran = PembayaranDaftarUlang::where(
                'order_id',
                $orderId
            )->first();

            if (!$pembayaran) {
                return response()->json([
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            // Pembayaran berhasil
            if (
                $transactionStatus === 'settlement' ||
                (
                    $transactionStatus === 'capture' &&
                    $fraudStatus === 'accept'
                )
            ) {
                $pembayaran->update([
                    'status' => 'lunas',
                    'transaction_id' => $notification->transaction_id,
                    'metode_pembayaran' => $notification->payment_type,
                    'paid_at' => now(),
                ]);
            }

            // Pembayaran masih menunggu
            elseif ($transactionStatus === 'pending') {
                $pembayaran->update([
                    'status' => 'pending',
                ]);
            }

            // Pembayaran gagal
            elseif (
                $transactionStatus === 'cancel' ||
                $transactionStatus === 'deny'
            ) {
                $pembayaran->update([
                    'status' => 'gagal',
                ]);
            }

            // Pembayaran kedaluwarsa
            elseif ($transactionStatus === 'expire') {
                $pembayaran->update([
                    'status' => 'kedaluwarsa',
                ]);
            }

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}