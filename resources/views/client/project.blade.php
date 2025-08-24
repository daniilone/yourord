@extends('layouts.client')

@section('title', 'Проект {{ $project->name }} - YourOrd')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ $project->name }}</h2>

        @if (session('message'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if (!Auth::guard('client')->check())
            <p class="text-gray-600 mb-4">Пожалуйста, <a href="{{ route('client.login') }}" class="text-indigo-600">войдите</a>, чтобы забронировать услугу.</p>
        @endif

        <form method="GET" action="{{ route('client.project', $project->slug) }}" class="mb-6">
            <label for="date" class="block text-gray-600">Выберите дату:</label>
            <input type="date" name="date" value="{{ $date }}" class="border rounded p-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Показать слоты</button>
        </form>

        @foreach ($services as $service)
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-700">{{ $service->name }} ({{ $service->duration }} минут, {{ $service->price }} руб.)</h3>
                @if (empty($slotsByService[$service->id]))
                    <p class="text-gray-600">Нет доступных слотов на {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}.</p>
                @else
                    <form method="POST" action="{{ route('project.booking', $project->slug) }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <select name="slot_start" class="border rounded p-2 mb-4">
                            @foreach ($slotsByService[$service->id] as $slot)
                                <option value="{{ $slot['start'] }}">{{ $slot['start'] }} - {{ $slot['end'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700" @if (!Auth::guard('client')->check()) disabled @endif>Забронировать</button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
@endsection
