<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои проекты - YourOrd</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4B5EAA',
                        secondary: '#6B7280',
                        accent: '#34D399',
                        background: '#F3F4F6',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' },
                        }
                    }
                }
            }
        }

        async function addToFavorites(slug, token) {
            try {
                const response = await axios.post(`/client/project/${slug}/favorite`, { _token: token }, {
                    headers: { 'X-CSRF-TOKEN': token }
                });
                alert(response.data.message);
                window.location.reload();
            } catch (error) {
                alert('Ошибка: ' + (error.response?.data?.error || 'Не удалось добавить в избранное'));
            }
        }
    </script>
</head>
<body class="bg-background font-sans antialiased">
<div class="min-h-screen flex">
    <!-- Боковое меню -->
    <div x-data="{ open: false }" class="fixed inset-y-0 left-0 w-64 bg-primary text-white transform transition-transform duration-300 ease-in-out"
         :class="{ 'translate-x-0': open, '-translate-x-full': !open }" @click.away="open = false">
        <div class="p-4 flex items-center justify-between">
            <h1 class="text-xl font-bold">YourOrd</h1>
            <button @click="open = false" class="sm:hidden text-white hover:text-secondary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <nav class="mt-4">
            <a href="{{ route('client.dashboard') }}" class="flex items-center px-4 py-2 hover:bg-secondary hover:text-white transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Дашборд
            </a>
            <a href="{{ route('client.bookings') }}" class="flex items-center px-4 py-2 hover:bg-secondary hover:text-white transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Мои записи
            </a>
            <a href="{{ route('client.projects') }}" class="flex items-center px-4 py-2 hover:bg-secondary hover:text-white transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Проекты
            </a>
            <form action="{{ route('client.auth.logout') }}" method="POST" class="px-4 py-2">
                @csrf
                <button type="submit" class="flex items-center w-full text-left hover:bg-secondary hover:text-white transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Выйти
                </button>
            </form>
        </nav>
    </div>

    <!-- Кнопка меню для мобильных -->
    <button x-data="{ open: false }" @click="open = true" class="sm:hidden fixed top-4 left-4 z-50 p-2 bg-primary text-white rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- Основной контент -->
    <div class="flex-1 p-4 sm:pl-64">
        <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 animate-fade-in">Мои проекты</h2>
            <div class="bg-white shadow-xl rounded-lg overflow-hidden animate-fade-in">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-700">Список избранных проектов</h3>
                </div>
                @if ($projects->isEmpty())
                    <p class="p-6 text-gray-600">У вас пока нет избранных проектов.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="p-4">Название</th>
                                <th class="p-4 hidden sm:table-cell">Описание</th>
                                <th class="p-4 hidden md:table-cell">Мастер</th>
                                <th class="p-4">Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($projects as $project)
                                <tr class="border-b hover:bg-gray-50 transition duration-200">
                                    <td class="p-4">
                                        <a href="{{ route('client.project', $project->slug) }}" class="text-accent hover:underline">
                                            {{ $project->name }}
                                        </a>
                                    </td>
                                    <td class="p-4 hidden sm:table-cell">{{ $project->description ?? 'Нет описания' }}</td>
                                    <td class="p-4 hidden md:table-cell">{{ $project->master->name ?? $project->master->email }}</td>
                                    <td class="p-4">
                                        <button onclick="addToFavorites('{{ $project->slug }}', '{{ csrf_token() }}')"
                                                class="text-accent hover:text-green-600 text-sm font-medium transition duration-200">
                                            {{ $project->clients->contains(auth()->guard('client')->id()) ? 'Удалить из избранного' : 'Добавить в избранное' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6">
                        {{ $projects->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<footer class="bg-gray-800 text-white text-center p-6">
    <p>&copy; {{ date('Y') }} YourOrd. Все права защищены.</p>
</footer>
</body>
</html>
