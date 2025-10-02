<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['transaction', 'route'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'passenger_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'travel_date' => 'required|date|after_or_equal:today',
            'seats' => 'required|integer|min:1|max:10',
        ]);

        $route = \App\Models\Route::findOrFail($request->route_id);
        $amount = $route->price * $request->seats;

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'route_id' => $request->route_id,
            'booking_number' => Booking::generateBookingNumber(),
            'passenger_name' => $request->passenger_name,
            'phone' => $request->phone,
            'travel_date' => $request->travel_date,
            'seats' => $request->seats,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        // Create transaction
        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'user_id' => $request->user()->id,
            'transaction_number' => Transaction::generateTransactionNumber(),
            'amount' => $amount,
            'payment_method' => 'gcash',
            'payment_status' => 'pending',
        ]);

        return response()->json([
            'booking' => $booking,
            'transaction' => $transaction,
        ], 201);
    }

    public function show($id)
    {
        $booking = Booking::with(['transaction', 'route'])->findOrFail($id);

        // Check if user owns this booking
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($booking);
    }

    public function uploadReceipt(Request $request, $id)
    {
        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $booking = Booking::findOrFail($id);

        // Check if user owns this booking
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $transaction = $booking->transaction;

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Delete old receipt if exists
        if ($transaction->receipt_image) {
            Storage::disk('public')->delete($transaction->receipt_image);
        }

        // Store new receipt
        $path = $request->file('receipt')->store('receipts', 'public');
        $transaction->receipt_image = $path;
        $transaction->save();

        return response()->json([
            'message' => 'Receipt uploaded successfully',
            'transaction' => $transaction,
        ]);
    }

    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        // Check if user owns this booking
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($booking->status === 'confirmed') {
            return response()->json(['message' => 'Cannot cancel confirmed booking'], 400);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'booking' => $booking,
        ]);
    }
}
