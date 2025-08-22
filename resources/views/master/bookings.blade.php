@extends('layouts.master')

@section('title', 'Записи - YourOrd')

@section('header', 'Ваши записи')

@push('styles')
    <style>
        .table-container {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
        }
        .table-header {
            background-color: #f9fafb;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 500;
            color: #6b7280;
            letter-spacing: 0.05em;
        }
        .table-cell {
            padding: 1rem 1.5rem;
            font-size: 0.875rem;
            color: #111827;
            white-space: nowrap;
        }
        .action-button {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: background-color 0.2s ease-in-out;
        }
        .pagination {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Ваши записи</h2>

        <div class="table-container">
            @if ($bookings->isEmpty())
                <p class="text-gray-600 text-sm">Записи отсутствуют.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="table-header">
                    <tr>
                        <th class="table-cell text-left">Проект</th>
                        <th class="table-cell text-left">Услуга</th>
                        <th class="table-cell text-left">Клиент</th>
                        <th class="table-cell text-left">Дата</th>
                        <th class="table-cell text-left">Время</th>
                        <th class="table-cell text-left">Статус</th>
                        <th class="table-cell text-left">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @foreach ($bookings as $booking)
                        <tr>
                            <td class="table-cell">{{ $booking->project->name }}</td>
                            <td class="table-cell">{{ $booking->service->name }}</td>
                            <td class="table-cell">{{ $booking->client->name ?? $booking->client->email }}</td>
                            <td class="table-cell">{{ \Carbon\Carbon::parse($booking->dailySchedule->date)->format('d.m.Y') }}</td>
                            <td class="table-cell">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                            <td class="table-cell">
                                <span class="{{ $booking->status == 'confirmed' ? 'text-green-600' : ($booking->status == 'cancelled' ? 'text-red-600' : 'text-yellow-600') }}">
                                    {{ $booking->status == 'pending' ? 'Ожидает' : ($booking->status == 'confirmed' ? 'Подтверждено' : 'Отменено') }}
                                </span>
                            </td>
                            <td class="table-cell">
                                <form method="POST" action="{{ route('master.bookings.update', $booking->id) }}" class="inline-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Ожидает</option>
                                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Подтверждено</option>
                                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Отменено</option>
                                    </select>
                                    <button type="submit" class="action-button bg-indigo-600 text-white hover:bg-indigo-700">Обновить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pagination">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
