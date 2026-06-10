<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $general     = Setting::where('group', 'general')->get()->keyBy('key');
        $payment     = Setting::where('group', 'payment')->get()->keyBy('key');
        $transaction = Setting::where('group', 'transaction')->get()->keyBy('key');
        $receipt     = Setting::where('group', 'receipt')->get()->keyBy('key');

        return view('admin.settings.index', compact(
            'general', 'payment', 'transaction', 'receipt'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cafe_name'     => 'required|string|max:255',
            'cafe_phone'    => 'nullable|string|max:20',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'qris_server_key' => 'nullable|string',
            'qris_client_key' => 'nullable|string',
        ]);

        // Handle upload logo
        if ($request->hasFile('cafe_logo')) {
            $request->validate(['cafe_logo' => 'image|mimes:jpg,jpeg,png|max:1024']);
            $old = Setting::get('cafe_logo');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('cafe_logo')->store('settings', 'public');
            Setting::set('cafe_logo', $path);
        }

        // Handle upload QRIS image
        if ($request->hasFile('qris_image')) {
            $request->validate(['qris_image' => 'image|mimes:jpg,jpeg,png|max:1024']);
            $old = Setting::get('qris_image');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('qris_image')->store('settings', 'public');
            Setting::set('qris_image', $path);
        }

        // Simpan semua setting
        $keys = [
            'cafe_name', 'cafe_address', 'cafe_phone',
            'payment_cash', 'payment_qris',
            'qris_type', 'qris_provider', 'qris_server_key', 'qris_client_key', 'qris_mode',
            'tax_percentage', 'discount_percentage', 'order_prefix',
            'receipt_header', 'receipt_footer', 'receipt_show_logo',
        ];

        foreach ($keys as $key) {
            // Boolean fields
            if (in_array($key, ['payment_cash', 'payment_qris', 'receipt_show_logo'])) {
                Setting::set($key, $request->has($key) ? '1' : '0');
            } else {
                Setting::set($key, $request->input($key, ''));
            }
        }

        return redirect()->route('admin.settings.index')
                         ->with('success', 'Pengaturan berhasil disimpan.');
    }
}