@extends('admin.layout')

@section('title', 'Routes - TappyPass Admin')
@section('page-title', 'Manage Routes')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.routes.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded inline-flex items-center">
        <i class="fas fa-plus mr-2"></i>Add New Route
    </a>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">All Routes</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departure Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Available Seats</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($routes as $route)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $route->from_location }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $route->to_location }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ date('h:i A', strtotime($route->departure_time)) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">₱{{ number_format($route->price, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $route->available_seats }}</td>
                    <td class="px-6 py-4 text-sm">
                        <form action="{{ route('admin.routes.toggle-status', $route->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2 py-1 rounded-full text-xs {{ $route->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $route->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('admin.routes.edit', $route->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.routes.destroy', $route->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this route?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No routes found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $routes->links() }}
    </div>
</div>
@endsection
