@extends('layouts.client')
@section('title', 'Мои проекты - YourOrd')
@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Мои проекты</h1>
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">Список избранных проектов</h2>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Мастер</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($projects as $project)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $project->master->name ?? $project->master->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('client.project', $project->slug) }}" class="text-indigo-600 hover:text-indigo-800">Просмотреть</a>
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
