@extends('layouts.specialist')
@section('title', 'Создать проект - YourOrd')
@section('content')
    <div class="max-w-md mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Создать проект</h1>
        <form method="POST" action="{{ route('specialist.project.create') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Название</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Описание</label>
                <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Создать</button>
        </form>
    </div>
@endsection
