<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class RegenerateQrCodes extends Command
{
    protected $signature = 'qr:regenerate';
    protected $description = 'Regenerate all QR codes with current APP_URL';

    public function handle()
    {
        $this->info('Regenerating QR codes...');
        
        $bookings = Booking::where('status', 'confirmed')
            ->whereNotNull('qr_code')
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            // Generate new QR code with current URL
            $qrCodePath = 'qrcodes/' . $booking->booking_number . '.svg';
            $bookingUrl = url('/booking/' . $booking->booking_number);
            
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->generate($bookingUrl);
            
            Storage::disk('public')->put($qrCodePath, $qrCode);
            $booking->qr_code = $qrCodePath;
            $booking->save();
            
            $count++;
            $this->line("Regenerated QR for booking: {$booking->booking_number}");
        }
        
        $this->info("Successfully regenerated {$count} QR codes!");
    }
}