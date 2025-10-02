@extends('admin.layout')

@section('title', 'Bookings - TappyPass Admin')
@section('page-title', 'Manage Bookings')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 md:mb-0">All Bookings</h3>
            
            <div class="flex flex-col md:flex-row gap-3">
                <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex gap-2">
                    <select name="status" class="border border-gray-300 rounded px-3 py-2">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                        class="border border-gray-300 rounded px-3 py-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Passenger</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Travel Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seats</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $booking->booking_number }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $booking->user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $booking->passenger_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $booking->route->from_location }} → {{ $booking->route->to_location }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $booking->travel_date->format('M d, Y') }} {{ date('h:i A', strtotime($booking->route->departure_time)) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $booking->seats }}</td>
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
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-6 py-4 text-center text-gray-500">No bookings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
