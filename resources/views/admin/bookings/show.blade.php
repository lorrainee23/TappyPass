@extends('admin.layout')

@section('title', 'Booking Details - TappyPass Admin')
@section('page-title', 'Booking Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings.index') }}" class="text-indigo-600 hover:text-indigo-900">
        <i class="fas fa-arrow-left"></i> Back to Bookings
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Booking Information -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Booking Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Booking Number</p>
                    <p class="font-semibold">{{ $booking->booking_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p>
                        @if($booking->status === 'confirmed')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Confirmed</span>
                        @elseif($booking->status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Cancelled</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Passenger Name</p>
                    <p class="font-semibold">{{ $booking->passenger_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="font-semibold">{{ $booking->phone }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Route</p>
                    <p class="font-semibold">{{ $booking->route->from_location }} → {{ $booking->route->to_location }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Departure Time</p>
                    <p class="font-semibold">{{ date('h:i A', strtotime($booking->route->departure_time)) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Travel Date</p>
                    <p class="font-semibold">{{ $booking->travel_date->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Number of Seats</p>
                    <p class="font-semibold">{{ $booking->seats }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Amount</p>
                    <p class="font-semibold text-green-600">₱{{ number_format($booking->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Booked By</p>
                    <p class="font-semibold">{{ $booking->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Booked On</p>
                    <p class="font-semibold">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        @if($booking->transaction)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h3>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500">Transaction Number</p>
                    <p class="font-semibold">{{ $booking->transaction->transaction_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Payment Status</p>
                    <p>
                        @if($booking->transaction->payment_status === 'paid')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Paid</span>
                        @elseif($booking->transaction->payment_status === 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Rejected</span>
                            @if($booking->transaction->rejection_count > 0)
                                <span class="ml-2 text-xs text-gray-600">({{ $booking->transaction->rejection_count }}/3 attempts)</span>
                            @endif
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Payment Method</p>
                    <p class="font-semibold uppercase">{{ $booking->transaction->payment_method }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Amount</p>
                    <p class="font-semibold">₱{{ number_format($booking->transaction->amount, 2) }}</p>
                </div>
            </div>

            @if($booking->transaction->receipt_image)
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-2">Payment Receipt</p>
                <img src="{{ asset('storage/' . $booking->transaction->receipt_image) }}" alt="Receipt" class="max-w-md rounded border">
            </div>
            @endif

            @if($booking->status !== 'cancelled')
            <form action="{{ route('admin.bookings.payment-status', $booking->id) }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Payment Status</label>
                    <select name="payment_status" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="pending" {{ $booking->transaction->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $booking->transaction->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="rejected" {{ $booking->transaction->payment_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                    <textarea name="admin_notes" rows="3" class="w-full border border-gray-300 rounded px-3 py-2">{{ $booking->transaction->admin_notes }}</textarea>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                    Update Payment Status
                </button>
            </form>
            @else
            <div class="mt-4 p-4 bg-gray-100 rounded">
                <p class="text-gray-600"><strong>Note:</strong> Cannot update payment status for cancelled bookings.</p>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
            
            @if($booking->status === 'pending' && $booking->transaction && $booking->transaction->payment_status === 'paid')
            <form action="{{ route('admin.bookings.confirm', $booking->id) }}" method="POST" class="mb-4">
                @csrf
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>Confirm Booking
                </button>
            </form>
            @endif

            @if($booking->status === 'confirmed' && $booking->qr_code)
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-2">Booking QR Code</p>
                <img src="{{ asset('storage/' . $booking->qr_code) }}" alt="QR Code" class="w-full rounded border">
            </div>
            @endif

            <div class="text-sm text-gray-600">
                @if($booking->status === 'pending')
                    <p class="mb-2"><i class="fas fa-info-circle text-blue-500"></i> Waiting for payment confirmation</p>
                @elseif($booking->status === 'confirmed')
                    <p class="mb-2"><i class="fas fa-check-circle text-green-500"></i> Booking is confirmed</p>
                @else
                    <p class="mb-2"><i class="fas fa-times-circle text-red-500"></i> Booking is cancelled</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
