@extends('layouts.specialist')
@section('title', $project->name . ' - YourOrd')
@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">{{ $project->name }}</h1>
        <p class="mb-4">Баланс: {{ $project->balance }} руб.</p>
        @if (auth('specialist')->user()->projects()->where('project_id', $project->id)->first()->pivot->is_owner)
            <div class="mb-4 flex space-x-4">
                <a href="{{ route('specialist.project.edit', $project->slug) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Редактировать</a>
                <a href="{{ route('specialist.project.invite', $project->slug) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Пригласить специалиста</a>
                <a href="{{ route('specialist.project.permissions', $project->slug) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Управление правами</a>
                <a href="{{ route('specialist.project.balance', $project->slug) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Пополнить баланс</a>
            </div>
        @endif
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Расписание на {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</h2>
        @if ($dailySchedule)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Услуга</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Время</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($bookings as $booking)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $booking->service->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $booking->start_time }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium {{ $booking->status == 'confirmed' ? 'text-green-600' : ($booking->status == 'pending' ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $booking->status == 'pending' ? 'Ожидает' : ($booking->status == 'confirmed' ? 'Подтверждено' : 'Отменено') }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($booking->status == 'pending' && auth('specialist')->user()->projects()->where('project_id', $project->id)->first()->pivot->permissions && in_array('confirm_bookings', auth('specialist')->user()->projects()->where('project_id', $project->id)->first()->pivot->permissions))
                                    <form action="{{ route('specialist.project.confirm_booking', [$project->slug, $booking]) }}" method="POST" class="inline">
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
        @else
            <p class="text-gray-600">Расписание на эту дату отсутствует.</p>
        @endif
        @if (auth('specialist')->user()->projects()->where('project_id', $project->id)->first()->pivot->permissions && in_array('manual_bookings', auth('specialist')->user()->projects()->where('project_id', $project->id)->first()->pivot->permissions))
            <h2 class="text-2xl font-semibold mb-4 mt-6 text-gray-700">Добавить запись вручную</h2>
            <form method="POST" action="{{ route('specialist.project.manual_booking', $project->slug) }}" class="space-y-4">
                @csrf
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700">Клиент</label>
                    <select name="client_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach (\App\Models\Client::all() as $client)
                            <option value="{{ $client->id }}">{{ $client->name ?? $client->email }}</option>
                        @endforeach
                    </select>
                    @error('client_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="service_id" class="block text-sm font-medium text-gray-700">Услуга</label>
                    <select name="service_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach ($project->services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                    @error('service_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Дата</label>
                    <input type="date" name="date" value="{{ old('date', $date) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('date')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Время</label>
                    <input type="time" name="start_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('start_time')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Добавить</button>
            </form>
        @endif
    </div>
@endsection
