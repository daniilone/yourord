@extends('layouts.specialist')

@section('title', 'Дашборд специалиста')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold text-primary mb-6">Дашборд специалиста</h1>

        @if (session('success'))
            <div class="bg-success text-white p-4 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-xl font-semibold text-gray-700 mb-4">Ваши проекты</h2>
        @if ($projects->isEmpty())
            <p class="text-gray-600">У вас пока нет проектов.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <div class="bg-white p-4 rounded shadow">
                        <h3 class="text-lg font-semibold text-primary">{{ $project->title }}</h3>
                        <p class="text-gray-600">{{ $project->description ?? 'Без описания' }}</p>
                        <a href="{{ route('specialist.projects') }}" class="text-primary hover:underline">Подробнее</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
