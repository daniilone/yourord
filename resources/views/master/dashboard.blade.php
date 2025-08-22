@extends('layouts.master')

@section('title', 'Панель управления')

@section('header', 'Дашборд')

@section('content')
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Добро пожаловать, {{ $master->name ?? $master->email }}
        </h3>
        <div class="mt-2 max-w-xl text-sm text-gray-500">
            <p>Здесь вы можете управлять своими проектами, расписанием и записями клиентов.</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-5 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Проекты -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                    <i class="fas fa-project-diagram text-white text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Всего проектов
                        </dt>
                        <dd>
                            <div class="text-lg font-medium text-gray-900">
                                {{ $projects->count() }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('master.projects') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Управление проектами
                </a>
            </div>
        </div>
    </div>

    <!-- Записи на сегодня -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <i class="fas fa-calendar-check text-white text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Записи на сегодня
                        </dt>
                        <dd>
                            <div class="text-lg font-medium text-gray-900">
                                {{ $todayBookings }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('master.bookings') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Просмотреть все записи
                </a>
            </div>
        </div>
    </div>

    <!-- Услуги -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <i class="fas fa-concierge-bell text-white text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Всего услуг
                        </dt>
                        <dd>
                            <div class="text-lg font-medium text-gray-900">
                                {{ $servicesCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('master.services') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Управление услугами
                </a>
            </div>
        </div>
    </div>

    <!-- Доходы -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <i class="fas fa-wallet text-white text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Доход за месяц
                        </dt>
                        <dd>
                            <div class="text-lg font-medium text-gray-900">
                                {{ number_format($monthlyEarnings, 0, ',', ' ') }} ₽
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Детализация
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Ближайшие записи -->
<div class="mt-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h2 class="text-xl font-semibold text-gray-900">Ближайшие записи</h2>
            <p class="mt-2 text-sm text-gray-700">Список предстоящих записей на ближайшие дни</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('master.bookings') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Все записи
            </a>
        </div>
    </div>
    <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Дата и время
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Клиент
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Услуга
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($upcomingBookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $booking->start_time->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100">
                                            <span class="text-indigo-600 font-medium">{{ substr($booking->client->name, 0, 1) }}</span>
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->client->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->client->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $booking->service->name ?? 'Услуга не указана' }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->project->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ][$booking->status] ?? 'bg-gray-100 text-gray-800';
                                    
                                    $statusLabels = [
                                        'pending' => 'Ожидает подтверждения',
                                        'confirmed' => 'Подтверждена',
                                        'completed' => 'Завершена',
                                        'cancelled' => 'Отменена',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Нет предстоящих записей
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Быстрый доступ -->
<div class="mt-8">
    <h2 class="text-xl font-semibold text-gray-900">Быстрый доступ</h2>
    <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('master.daily_schedules') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <i class="far fa-calendar-alt text-white text-2xl"></i>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Расписание</h3>
                        <p class="mt-1 text-sm text-gray-500">Управление вашим расписанием</p>
                    </div>
                </div>
            </div>
        </a>
        
        <a href="{{ route('master.services') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <i class="fas fa-concierge-bell text-white text-2xl"></i>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Услуги</h3>
                        <p class="mt-1 text-sm text-gray-500">Управление вашими услугами</p>
                    </div>
                </div>
            </div>
        </a>
        
        <a href="{{ route('master.projects') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <i class="fas fa-project-diagram text-white text-2xl"></i>
                    </div>
                    <div class="ml-5">
                        <h3 class="text-lg font-medium text-gray-900">Проекты</h3>
                        <p class="mt-1 text-sm text-gray-500">Управление вашими проектами</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
