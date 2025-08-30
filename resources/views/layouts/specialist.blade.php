<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #F3F4F6;
            color: #6B7280;
        }
        .bg-primary { background-color: #4B5EAA; }
        .text-primary { color: #4B5EAA; }
        .bg-success { background-color: #34D399; }
        .sidebar {
            background-color: #ffffff;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            display: block;
            padding: 1rem;
            color: #6B7280;
            font-weight: 500;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #4B5EAA;
            color: #ffffff;
        }
        .content {
            margin-left: 250px;
            padding: 2rem;
        }
    </style>
</head>
<body>
<div class="flex">
    <div class="sidebar">
        <div class="p-4">
            <a href="{{ route('specialist.dashboard') }}" class="text-2xl font-bold text-primary">YourOrd</a>
        </div>
        <nav class="mt-4">
            <a href="{{ route('specialist.dashboard') }}" class="{{ request()->routeIs('specialist.dashboard') ? 'active' : '' }}">Дашборд</a>
            <a href="{{ route('specialist.projects') }}" class="{{ request()->routeIs('specialist.projects') ? 'active' : '' }}">Проекты</a>
            <a href="{{ route('specialist.bookings') }}" class="{{ request()->routeIs('specialist.bookings') ? 'active' : '' }}">Записи</a>
            <a href="{{ route('specialist.schedule') }}" class="{{ request()->routeIs('specialist.schedule') ? 'active' : '' }}">Расписание</a>
            @auth('specialist')
                <form method="POST" action="{{ route('specialist.logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full text-left p-4 text-gray-500 hover:bg-primary hover:text-white">Выйти</button>
                </form>
            @else
                <a href="{{ route('specialist.login') }}" class="p-4">Вход</a>
            @endauth
        </nav>
    </div>
    <div class="content">
        <main>
            @if (session('success'))
                <div class="bg-success text-white p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
