@extends('layouts.specialist')

@section('title', 'Создать проект')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold text-primary mb-6">Создать новый проект</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('specialist.projects.store') }}">
            @csrf
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-semibold">Название проекта</label>
                <input type="text" name="title" id="title" class="w-full p-2 border rounded" value="{{ old('title') }}" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-semibold">Описание</label>
                <textarea name="description" id="description" class="w-full p-2 border rounded">{{ old('description') }}</textarea>
            </div>
            <div class="mb-4">
                <label for="client_id" class="block text-gray-700 font-semibold">Клиент</label>
                <select name="client_id" id="client_id" class="w-full p-2 border rounded" required>
                    <option value="">Выберите клиента</option>
                    @foreach (\App\Models\Client::all() as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Создать</button>
        </form>
    </div>
@endsection
