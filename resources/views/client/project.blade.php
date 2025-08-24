@extends('layouts.app')

@section('title', 'Проект {{ $project->name }} - YourOrd')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ $project->name }}</h2>

        @if (session('message'))
            <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if (!Auth::guard('client')->check())
            <p class="text-gray-600 mb-4">Пожалуйста, <a href="{{ route('client.auth.login') }}" class="text-indigo-600">войдите</a>, чтобы забронировать или добавить в избранное.</p>
        @else
            <form method="POST" action="{{ route('client.project.favorite', $project->slug) }}" class="mb-4">
                @csrf
                <button type="submit" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">Добавить в избранное</button>
            </form>
        @endif

        <form method="GET" action="{{ route('client.project', $project->slug) }}" class="mb-6">
            <label for="date" class="block text-gray-600">Выберите дату:</label>
            <input type="date" name="date" value="{{ $date }}" class="border rounded p-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Показать слоты</button>
        </form>

        @forelse ($services as $service)
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-700">{{ $service->name }} ({{ $service->duration }} минут, {{ $service->price }} руб.)</h3>
                @if (empty($slotsByService[$service->id]))
                    <p class="text-gray-600">Нет доступных слотов на {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}.</p>
                @else
                    <form method="POST" action="{{ route('client.project.booking', $project->slug) }}">
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
        @empty
            <p class="text-gray-600">Услуги не найдены.</p>
        @endforelse
    </div>
@endsection
