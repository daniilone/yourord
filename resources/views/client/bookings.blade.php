@extends('layouts.client')
@section('title', 'Мои записи - YourOrd')
@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Мои записи</h1>
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
                                    {{ $booking->status }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($booking->status != 'cancelled')
                                <form action="{{ route('client.bookings.cancel', $booking) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Отменить</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
