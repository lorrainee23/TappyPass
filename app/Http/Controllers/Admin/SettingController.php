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
        $gcashQrCode = Setting::get('gcash_qr_code');
        
        return view('admin.settings.index', compact('gcashQrCode'));
    }

    public function updateGcashQr(Request $request)
    {
        $request->validate([
            'gcash_qr' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Delete old QR code if exists
        $oldQrCode = Setting::get('gcash_qr_code');
        if ($oldQrCode) {
            Storage::disk('public')->delete($oldQrCode);
        }

        // Store new QR code
        $path = $request->file('gcash_qr')->store('gcash', 'public');
        Setting::set('gcash_qr_code', $path);

        return back()->with('success', 'GCash QR Code updated successfully!');
    }
}
