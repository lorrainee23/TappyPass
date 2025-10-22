<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CleanQrCodesSeeder extends Seeder
{
    public function run()
    {
        // Clean up old QR codes when seeding
        if (Storage::disk('public')->exists('qrcodes')) {
            Storage::disk('public')->deleteDirectory('qrcodes');
            $this->command->info('Cleaned up old QR codes');
        }
    }
}
