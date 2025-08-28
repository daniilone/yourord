<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'YourOrd - Клиентская панель')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .sidebar {
            background-color: #4B5EAA; /* primary */
        }
        .sidebar a:hover {
            background-color: #6B7280; /* secondary */
        }
        .sidebar a.active {
            background-color: #34D399; /* accent */
        }
        body {
            background-color: #F3F4F6; /* background */
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased">
<div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
    <!-- Sidebar -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="sidebar fixed inset-y-0 left-0 w-64 text-white transform transition-transform duration-300 ease-in-out md:translate-x-0 z-30">
        <div class="p-4 flex items-center justify-between">
            <h2 class="text-xl font-bold">YourOrd</h2>
            <button @click="sidebarOpen = false" class="md:hidden text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <nav class="mt-4">
            <a href="{{ route('client.dashboard') }}" class="block py-2 px-4 hover:text-white {{ request()->routeIs('client.dashboard') ? 'active text-white' : '' }}">Дашборд</a>
            <a href="{{ route('client.bookings') }}" class="block py-2 px-4 hover:text-white {{ request()->routeIs('client.bookings') ? 'active text-white' : '' }}">Мои записи</a>
            <a href="{{ route('client.projects') }}" class="block py-2 px-4 hover:text-white {{ request()->routeIs('client.projects') ? 'active text-white' : '' }}">Мои проекты</a>
            <form method="POST" action="{{ route('client.auth.logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left py-2 px-4 hover:text-white">Выйти</button>
            </form>
        </nav>
    </div>

    <!-- Mobile menu button -->
    <div class="md:hidden fixed top-4 left-4 z-40">
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-800 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
            </svg>
        </button>
    </div>

    <!-- Main content -->
    <main class="flex-1 ml-0 md:ml-64 p-6">
        @if (session('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('message') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>
