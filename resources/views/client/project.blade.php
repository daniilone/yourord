<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }} - YourOrd</title>
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

        async function createBooking(slug, formData, token) {
            try {
                const response = await axios.post(`/client/project/${slug}/booking`, formData, {
                    headers: { 'X-CSRF-TOKEN': token }
                });
                alert(response.data.message);
                window.location.reload();
            } catch (error) {
                alert('Ошибка: ' + (error.response?.data?.message || 'Не удалось создать запись'));
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
            <h2 class="text-3xl font-bold text-gray-800 mb-6 animate-fade-in">{{ $project->name }}</h2>

            <div class="bg-white shadow-xl rounded-lg p-6 mb-6 animate-fade-in">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Описание</h3>
                <p class="text-gray-600">{{ $project->description ?? 'Нет описания' }}</p>
            </div>

            <div class="bg-white shadow-xl rounded-lg p-6 mb-6 animate-fade-in">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Категории и услуги</h3>
                @foreach ($categories as $category)
                    <div class="mb-4">
                        <h4 class="text-lg font-medium text-gray-600">{{ $category->name }}</h4>
                        <ul class="list-disc pl-6 mt-2">
                            @foreach ($services->where('category_id', $category->id) as $service)
                                <li class="text-gray-600">{{ $service->name }} ({{ $service->duration }} мин, {{ $service->price }} руб.)</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            <div x-data="{ date: '{{ $date }}', serviceId: '', slot: '' }" class="bg-white shadow-xl rounded-lg p-6 animate-fade-in">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Записаться</h3>
                <div class="flex flex-col sm:flex-row gap-4 mb-4">
                    <input x-model="date" @change="window.location.href = `?date=${$event.target.value}`"
                           type="date" class="border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-accent">
                    <select x-model="serviceId" class="border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-accent">
                        <option value="">Выберите услугу</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                    <select x-model="slot" class="border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-accent">
                        <option value="">Выберите время</option>
                        <template x-for="slot in {{ json_encode($slotsByService) }}[serviceId] || []" :key="slot.start">
                            <option :value="slot.start" x-text="slot.start"></option>
                        </template>
                    </select>
                </div>
                @error('service_id')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
                @error('date')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
                @error('start_time')
                <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
                <button @click="if (serviceId && slot) createBooking('{{ $project->slug }}', { service_id: serviceId, date: date, slot_start: slot, _token: '{{ csrf_token() }}' }, '{{ csrf_token() }}')"
                        class="bg-accent text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-300">Записаться</button>
            </div>
        </div>
    </div>
</div>

<footer class="bg-gray-800 text-white text-center p-6">
    <p>&copy; {{ date('Y') }} YourOrd. Все права защищены.</p>
</footer>
</body>
</html>
