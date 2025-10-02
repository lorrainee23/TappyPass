@extends('admin.layout')

@section('title', 'Edit Route - TappyPass Admin')
@section('page-title', 'Edit Route')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.routes.index') }}" class="text-indigo-600 hover:text-indigo-900">
        <i class="fas fa-arrow-left"></i> Back to Routes
    </a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('admin.routes.update', $route->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">From Location *</label>
            <input type="text" name="from_location" value="{{ old('from_location', $route->from_location) }}" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('from_location')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">To Location *</label>
            <input type="text" name="to_location" value="{{ old('to_location', $route->to_location) }}" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('to_location')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Departure Time *</label>
            <input type="time" name="departure_time" value="{{ old('departure_time', $route->departure_time) }}" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('departure_time')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Price (₱) *</label>
            <input type="number" name="price" value="{{ old('price', $route->price) }}" step="0.01" min="0" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('price')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Available Seats *</label>
            <input type="number" name="available_seats" value="{{ old('available_seats', $route->available_seats) }}" min="1" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('available_seats')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $route->is_active) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded">
                <i class="fas fa-save mr-2"></i>Update Route
            </button>
            <a href="{{ route('admin.routes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
