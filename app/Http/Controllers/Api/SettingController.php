<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getGcashQr()
    {
        $qrCode = Setting::get('gcash_qr_code');

        return response()->json([
            'qr_code' => $qrCode ? asset('storage/' . $qrCode) : null,
        ]);
    }
}
