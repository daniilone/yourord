@extends('layouts.specialist')
@section('title', 'Специалист - Дашборд - YourOrd')
@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Добро пожаловать, {{ $specialist->name ?? $specialist->email }}</h1>
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Ваши проекты</h2>
        <div class="mb-4">
            <a href="{{ route('specialist.project.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Создать проект</a>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Баланс</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($projects as $project)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->balance }} руб.</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('specialist.project', $project->slug) }}" class="text-indigo-600 hover:text-indigo-800">Просмотреть</a>
                            @if ($project->specialists()->where('specialist_id', auth('specialist')->id())->first()->pivot->is_owner)
                                <form action="{{ route('specialist.project.delete', $project->slug) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 ml-2">Удалить</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $projects->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
