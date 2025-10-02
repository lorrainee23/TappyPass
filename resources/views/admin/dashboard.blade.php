@extends('admin.layout')

@section('title', 'Dashboard - TappyPass Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <!-- Total Bookings -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Bookings</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_bookings'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-ticket-alt text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Pending Bookings -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Bookings</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_bookings'] }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Confirmed Bookings -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Confirmed Bookings</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['confirmed_bookings'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Users</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-users text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Payments</p>
                <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_payments'] }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-money-bill-wave text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Revenue</p>
                <p class="text-3xl font-bold text-green-600">₱{{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-peso-sign text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Recent Bookings</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($recent_bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $booking->booking_number }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $booking->user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $booking->from_location }} → {{ $booking->to_location }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $booking->travel_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">₱{{ number_format($booking->amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($booking->status === 'confirmed')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Confirmed</span>
                        @elseif($booking->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Cancelled</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($booking->transaction)
                            @if($booking->transaction->payment_status === 'paid')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Paid</span>
                            @elseif($booking->transaction->payment_status === 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Rejected</span>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No bookings yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
