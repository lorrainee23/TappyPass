<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TappyPass Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-indigo-800 text-white">
            <div class="p-6">
                <h1 class="text-2xl font-bold">TappyPass</h1>
                <p class="text-sm text-indigo-200">Admin Panel</p>
            </div>
            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-indigo-700 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-chart-line mr-3"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.routes.index') }}" class="flex items-center px-6 py-3 hover:bg-indigo-700 {{ request()->routeIs('admin.routes.*') ? 'bg-indigo-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-route mr-3"></i>
                    Routes
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-6 py-3 hover:bg-indigo-700 {{ request()->routeIs('admin.bookings.*') ? 'bg-indigo-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-ticket-alt mr-3"></i>
                    Bookings
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 hover:bg-indigo-700 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-users mr-3"></i>
                    Users
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center px-6 py-3 hover:bg-indigo-700 {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-700 border-l-4 border-white' : '' }}">
                    <i class="fas fa-cog mr-3"></i>
                    Settings
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center">
                        <span class="mr-4 text-gray-700">{{ auth()->user()->name }}</span>
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
