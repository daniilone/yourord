@extends('layouts.specialist')
@section('title', 'Записи - YourOrd')
@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Записи</h1>
        <div class="mb-4 flex space-x-4">
            <form method="GET" action="{{ route('specialist.bookings') }}" class="flex space-x-2">
                <select name="status" class="border-gray-300 rounded-md shadow-sm">
                    <option value="">Все статусы</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Ожидает</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Подтверждено</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Отменено</option>
                </select>
                <input type="date" name="date" value="{{ request('date') }}" class="border-gray-300 rounded-md shadow-sm">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Фильтр</button>
            </form>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Проект</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Услуга</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Время</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($bookings as $booking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->project->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->service->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ is_string($booking->dailySchedule->date) ? \Carbon\Carbon::parse($booking->dailySchedule->date)->format('d.m.Y') : $booking->dailySchedule->date->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->start_time }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium {{ $booking->status == 'confirmed' ? 'text-green-600' : ($booking->status == 'pending' ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $booking->status == 'pending' ? 'Ожидает' : ($booking->status == 'confirmed' ? 'Подтверждено' : 'Отменено') }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($booking->status == 'pending' && auth('specialist')->user()->projects()->where('project_id', $booking->project_id)->first()->pivot->permissions && in_array('confirm_bookings', auth('specialist')->user()->projects()->where('project_id', $booking->project_id)->first()->pivot->permissions))
                                <form action="{{ route('specialist.project.confirm_booking', [$booking->project->slug, $booking]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800">Подтвердить</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $bookings->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
