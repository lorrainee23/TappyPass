@extends('admin.layout')

@section('title', 'User Details - TappyPass Admin')
@section('page-title', 'User Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Information -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center mb-6">
                <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user text-indigo-600 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h3>
                <p class="text-gray-600">{{ $user->email }}</p>
            </div>

            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="font-semibold">{{ $user->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Member Since</p>
                    <p class="font-semibold">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Bookings</p>
                    <p class="font-semibold">{{ $user->bookings->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking History -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Booking History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($user->bookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $booking->booking_number }}
                                </a>
                            </td>
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
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No bookings yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
