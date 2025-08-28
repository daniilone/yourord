@extends('layouts.client')
@section('title', 'Вход - Клиент')
@section('content')
    <div class="max-w-md mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Вход</h1>
        <form method="POST" action="{{ route('client.auth.login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Телефон</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('phone')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Пароль (опционально)</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Войти</button>
        </form>
    </div>
@endsection
