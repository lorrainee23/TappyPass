@extends('admin.layout')

@section('title', 'Settings - TappyPass Admin')
@section('page-title', 'Settings')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">GCash Payment Settings</h3>

    <div class="max-w-2xl">
        @if($gcashQrCode)
        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-2">Current GCash QR Code</p>
            <img src="{{ asset('storage/' . $gcashQrCode) }}" alt="GCash QR Code" class="max-w-xs rounded border">
        </div>
        @endif

        <form action="{{ route('admin.settings.gcash-qr') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $gcashQrCode ? 'Update' : 'Upload' }} GCash QR Code
                </label>
                <input type="file" name="gcash_qr" accept="image/*" required
                    class="w-full border border-gray-300 rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Upload a QR code image for GCash payments (JPG, PNG)</p>
            </div>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded">
                <i class="fas fa-save mr-2"></i>{{ $gcashQrCode ? 'Update' : 'Upload' }} QR Code
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">About TappyPass</h3>
    <div class="text-gray-600">
        <p class="mb-2"><strong>Version:</strong> 1.0.0</p>
        <p class="mb-2"><strong>System:</strong> Bus Booking Management System</p>
        <p class="mb-2"><strong>Features:</strong></p>
        <ul class="list-disc list-inside ml-4">
            <li>Customer booking management</li>
            <li>GCash payment integration</li>
            <li>QR code generation for confirmed bookings</li>
            <li>Transaction history tracking</li>
            <li>User management</li>
        </ul>
    </div>
</div>
@endsection
