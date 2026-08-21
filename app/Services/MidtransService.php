<?php

namespace App\Services;

use App\Models\Setting;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        $serverKey = Setting::get('qris_server_key');

        // Dekripsi jika terenkripsi
        try {
            $serverKey = decrypt($serverKey);
        } catch (\Exception $e) {
            // Jika tidak terenkripsi, pakai langsung
        }

        Config::$serverKey    = $serverKey;
        Config::$isProduction = Setting::get('qris_mode', 'sandbox') === 'production';
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    // Buat transaksi QRIS
    public function createQrisTransaction(array $orderData): array
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $orderData['order_code'] . '-' . time(),
                'gross_amount' => (int) $orderData['final_amount'],
            ],
            'payment_type' => 'qris',
            'qris'         => [
                'acquirer' => 'airpay shopee', // ← ganti ini
            ],
            'customer_details' => [
                'first_name' => 'Customer',
                'email'      => 'customer@cafe.com',
            ],
        ];

        $response = \Midtrans\CoreApi::charge($params);

        // Ambil URL dari actions
        $qrCodeUrl = null;
        if (!empty($response->actions)) {
            foreach ($response->actions as $action) {
                $actionObj = is_object($action->stdClass ?? null) ? $action->stdClass : $action;
                if (isset($actionObj->name) && $actionObj->name === 'generate-qr-code') {
                    $qrCodeUrl = $actionObj->url;
                    break;
                }
            }
        }
        Log::info('Midtrans Response:', (array) $response);
        return [
            'transaction_id' => $response->transaction_id,
            'order_id'       => $response->order_id,
            'qr_string'      => $response->qr_string ?? null,
            'qr_code_url'    => $qrCodeUrl, // ← ganti ini
            'expire_time'    => $response->expiry_time ?? null,
            'status'         => $response->transaction_status,
        ];
    }
    // Cek status transaksi
    public function checkStatus(string $orderId): string
    {
        $status = Transaction::status($orderId);

        // Convert ke object jika berupa array
        if (is_array($status)) {
            $status = (object) $status;
        }

        return $status->transaction_status ?? 'pending';
    }
}