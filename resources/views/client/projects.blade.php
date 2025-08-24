@extends('layouts.app')

@section('title', 'Проекты - YourOrd')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Доступные проекты</h2>

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
            <p class="text-gray-600 mb-4">Пожалуйста, <a href="{{ route('client.auth.login') }}" class="text-indigo-600">войдите</a>, чтобы забронировать услугу.</p>
        @endif

        <form method="GET" action="{{ route('client.projects') }}" class="mb-6">
            <label for="date" class="block text-gray-600">Выберите дату:</label>
            <input type="date" name="date" value="{{ $date }}" class="border rounded p-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Показать слоты</button>
        </form>

        @forelse ($projects as $project)
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-700"><a href="{{ route('client.project', $project->slug) }}">{{ $project->name }}</a></h3>
                @forelse ($project->services as $service)
                    <div class="ml-4 mb-4">
                        <h4 class="text-lg font-medium text-gray-600">{{ $service->name }} ({{ $service->duration }} минут, {{ $service->price }} руб.)</h4>
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
                    <p class="text-gray-600 ml-4">Услуги не найдены.</p>
                @endforelse
            </div>
        @empty
            <p class="text-gray-600">Проекты не найдены.</p>
        @endforelse

        {{ $projects->links() }}
    </div>
@endsection
