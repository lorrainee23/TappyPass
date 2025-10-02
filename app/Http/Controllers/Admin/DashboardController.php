<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_users' => User::where('role', 'customer')->count(),
            'pending_payments' => Transaction::where('payment_status', 'pending')->count(),
            'total_revenue' => Transaction::where('payment_status', 'paid')->sum('amount'),
        ];

        $recent_bookings = Booking::with(['user', 'transaction'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings'));
    }
}
