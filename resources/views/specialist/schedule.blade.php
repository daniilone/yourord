@extends('layouts.specialist')
@section('title', 'Расписание - YourOrd')
@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Расписание</h1>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Проект</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Время работы</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Перерывы</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Записи</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($schedules as $schedule)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->project->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($schedule->date)->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @foreach ($schedule->workBreaks as $break)
                                {{ $break->start_time }} - {{ $break->end_time }}<br>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @foreach ($schedule->bookings as $booking)
                                {{ $booking->service->name }}: {{ $booking->start_time }} ({{ $booking->status == 'pending' ? 'Ожидает' : ($booking->status == 'confirmed' ? 'Подтверждено' : 'Отменено') }})<br>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
