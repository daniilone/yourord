<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
<nav class="bg-indigo-600 p-4">
    <div class="container mx-auto flex items-center justify-between">
        <a href="{{ route('client.projects') }}" class="text-white text-lg font-bold">YourOrd</a>
        <div>
            @auth('client')
                <a href="{{ route('client.dashboard') }}" class="text-white mr-4">Dashboard</a>
                <a href="{{ route('client.bookings') }}" class="text-white mr-4">Bookings</a>
                <form action="{{ route('client.auth.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-white">Logout</button>
                </form>
            @else
                <a href="{{ route('client.auth.login') }}" class="text-white mr-4">Login</a>
            @endauth
        </div>
    </div>
</nav>
<main class="container mx-auto px-4 py-8">
    @yield('content')
</main>
<footer class="bg-gray-800 text-white text-center p-4">
    <p>&copy; {{ now()->year }} YourOrd. All rights reserved.</p>
</footer>
</body>
</html>
