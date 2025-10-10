<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'transaction', 'route']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhere('passenger_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'transaction', 'route'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm($id)
    {
        $booking = Booking::findOrFail($id);
        $transaction = $booking->transaction;

        if (!$transaction || $transaction->payment_status !== 'paid') {
            return back()->with('error', 'Cannot confirm booking. Payment must be verified first.');
        }
        
        // Check if enough seats are available
        if ($booking->route->available_seats < $booking->seats) {
            return back()->with('error', 'Not enough seats available on this route.');
        }

        $booking->status = 'confirmed';
        
        // Decrement available seats from the route
        $booking->route->decrement('available_seats', $booking->seats);
        
        // Generate QR Code with booking confirmation details
        $qrCodePath = 'qrcodes/' . $booking->booking_number . '.svg';
        
        // Create a text message for the QR code (using only ASCII characters)
        $qrContent = "TAPPYPASS BOOKING CONFIRMED\n\n";
        $qrContent .= "Booking #: " . $booking->booking_number . "\n";
        $qrContent .= "Passenger: " . $booking->passenger_name . "\n";
        $qrContent .= "Route: " . $booking->route->from_location . " to " . $booking->route->to_location . "\n";
        $qrContent .= "Date: " . $booking->travel_date->format('M d, Y') . "\n";
        $qrContent .= "Time: " . date('h:i A', strtotime($booking->route->departure_time)) . "\n";
        $qrContent .= "Seats: " . $booking->seats . "\n";
        $qrContent .= "Amount: PHP " . number_format($booking->amount, 2) . "\n\n";
        $qrContent .= "STATUS: CONFIRMED\n";
        $qrContent .= "Show this QR code when boarding.";
        
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate($qrContent);
        
        Storage::disk('public')->put($qrCodePath, $qrCode);
        $booking->qr_code = $qrCodePath;
        $booking->save();

        return back()->with('success', 'Booking confirmed successfully!');
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $booking = Booking::findOrFail($id);
        $transaction = $booking->transaction;

        if (!$transaction) {
            return back()->with('error', 'Transaction not found.');
        }
        
        $oldStatus = $transaction->payment_status;
        $newStatus = $request->payment_status;

        // Increment rejection count when changing to rejected status
        // This happens when: pending->rejected OR paid->rejected
        if ($newStatus === 'rejected' && $oldStatus !== 'rejected') {
            // Initialize rejection_count if null
            if ($transaction->rejection_count === null) {
                $transaction->rejection_count = 0;
            }
            $transaction->rejection_count += 1;
            
            // Auto-cancel booking after 3 rejections
            if ($transaction->rejection_count >= 3) {
                $booking->status = 'cancelled';
                $booking->save();
                
                $transaction->payment_status = $newStatus;
                $transaction->admin_notes = $request->admin_notes . "\n\n[SYSTEM] Booking automatically cancelled after 3 payment rejections.";
                $transaction->save();
                
                return back()->with('warning', 'Payment rejected. Booking has been automatically cancelled after 3 rejections.');
            }
            
            // Save and show rejection count
            $transaction->payment_status = $newStatus;
            $transaction->admin_notes = $request->admin_notes;
            $transaction->save();
            
            return back()->with('success', "Payment rejected. Rejection count: {$transaction->rejection_count}/3");
        }

        $transaction->payment_status = $newStatus;
        $transaction->admin_notes = $request->admin_notes;
        $transaction->save();

        return back()->with('success', 'Payment status updated successfully!');
    }
}
