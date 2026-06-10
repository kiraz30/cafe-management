<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            [
                'key'   => 'cafe_name',
                'value' => 'Cafe Management',
                'type'  => 'text',
                'group' => 'general',
                'label' => 'Nama Cafe',
            ],
            [
                'key'   => 'cafe_address',
                'value' => '',
                'type'  => 'text',
                'group' => 'general',
                'label' => 'Alamat Cafe',
            ],
            [
                'key'   => 'cafe_phone',
                'value' => '',
                'type'  => 'text',
                'group' => 'general',
                'label' => 'No. Telepon',
            ],
            [
                'key'   => 'cafe_logo',
                'value' => '',
                'type'  => 'image',
                'group' => 'general',
                'label' => 'Logo Cafe',
            ],

            // Payment
            [
                'key'   => 'payment_cash',
                'value' => '1',
                'type'  => 'boolean',
                'group' => 'payment',
                'label' => 'Aktifkan Pembayaran Tunai',
            ],
            [
                'key'   => 'payment_qris',
                'value' => '1',
                'type'  => 'boolean',
                'group' => 'payment',
                'label' => 'Aktifkan Pembayaran QRIS',
            ],
            [
                'key'   => 'qris_type',
                'value' => 'static',
                'type'  => 'select',
                'group' => 'payment',
                'label' => 'Tipe QRIS',
            ],
            [
                'key'   => 'qris_image',
                'value' => '',
                'type'  => 'image',
                'group' => 'payment',
                'label' => 'Gambar QR Code (Statis)',
            ],
            [
                'key'   => 'qris_provider',
                'value' => 'midtrans',
                'type'  => 'select',
                'group' => 'payment',
                'label' => 'Provider QRIS Dinamis',
            ],
            [
                'key'   => 'qris_server_key',
                'value' => '',
                'type'  => 'text',
                'group' => 'payment',
                'label' => 'Server Key',
            ],
            [
                'key'   => 'qris_client_key',
                'value' => '',
                'type'  => 'text',
                'group' => 'payment',
                'label' => 'Client Key',
            ],
            [
                'key'   => 'qris_mode',
                'value' => 'sandbox',
                'type'  => 'select',
                'group' => 'payment',
                'label' => 'Mode QRIS',
            ],

            // Transaction
            [
                'key'   => 'tax_percentage',
                'value' => '0',
                'type'  => 'text',
                'group' => 'transaction',
                'label' => 'Persentase Pajak (%)',
            ],
            [
                'key'   => 'discount_percentage',
                'value' => '0',
                'type'  => 'text',
                'group' => 'transaction',
                'label' => 'Diskon Default (%)',
            ],
            [
                'key'   => 'order_prefix',
                'value' => 'ORD',
                'type'  => 'text',
                'group' => 'transaction',
                'label' => 'Prefix Nomor Order',
            ],

            // Receipt
            [
                'key'   => 'receipt_header',
                'value' => 'Terima kasih telah berkunjung!',
                'type'  => 'text',
                'group' => 'receipt',
                'label' => 'Header Struk',
            ],
            [
                'key'   => 'receipt_footer',
                'value' => 'Sampai jumpa kembali :)',
                'type'  => 'text',
                'group' => 'receipt',
                'label' => 'Footer Struk',
            ],
            [
                'key'   => 'receipt_show_logo',
                'value' => '1',
                'type'  => 'boolean',
                'group' => 'receipt',
                'label' => 'Tampilkan Logo di Struk',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}