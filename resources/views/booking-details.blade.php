<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - TappyPass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full mb-4">
                <i class="fas fa-bus text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">TappyPass</h1>
            <p class="text-gray-600 mt-2">Booking Confirmation</p>
        </div>

        @if($booking)
        <!-- Status Banner -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-green-800">Booking Confirmed</h3>
                    <p class="text-green-700">This booking has been confirmed and paid</p>
                </div>
            </div>
        </div>

        <!-- Booking Information Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-indigo-600">
                <h2 class="text-xl font-semibold text-white">Booking Information</h2>
            </div>
            
            <div class="p-6">
                <!-- Booking Number -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Booking Number</span>
                        <span class="text-lg font-bold text-indigo-600">{{ $booking->booking_number }}</span>
                    </div>
                </div>

                <!-- Route Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Route Details</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                                <span class="font-medium">{{ $booking->route->from_location }}</span>
                            </div>
                            <i class="fas fa-arrow-right text-gray-400"></i>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                                <span class="font-medium">{{ $booking->route->to_location }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Date:</span>
                                <span class="font-medium">{{ $booking->travel_date->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Time:</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($booking->route->departure_time)->format('h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passenger Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Passenger Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Passenger Name</span>
                            <p class="font-medium">{{ $booking->passenger_name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Phone</span>
                            <p class="font-medium">{{ $booking->phone }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Seats</span>
                            <p class="font-medium">{{ $booking->seats }} seat(s)</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Amount</span>
                            <p class="font-medium text-green-600">₱{{ number_format($booking->amount, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Status -->
                @if($booking->transaction)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm text-gray-500">Payment Status</span>
                                <p class="font-medium text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Paid
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Transaction #</span>
                                <p class="font-medium">{{ $booking->transaction->transaction_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Booking Date -->
                <div class="border-t pt-4">
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>Booked on</span>
                        <span>{{ $booking->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <h4 class="font-medium text-blue-900 mb-1">Boarding Instructions</h4>
                    <p class="text-blue-800 text-sm">Please arrive at the terminal 15 minutes before departure time. Show this booking confirmation or the QR code to the conductor when boarding.</p>
                </div>
            </div>
        </div>

        @else
        <!-- Booking Not Found -->
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Booking Not Found</h2>
            <p class="text-gray-600">The booking you're looking for doesn't exist or has been removed.</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} TappyPass. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
