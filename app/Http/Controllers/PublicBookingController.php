<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    /**
     * Display booking details for QR code scanning
     */
    public function show($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->where('status', 'confirmed')
            ->with(['route', 'transaction', 'user'])
            ->first();

        return view('booking-details', compact('booking'));
    }
}
