@extends('layouts.specialist')
@section('title', 'Вход для специалистов - YourOrd')
@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100" style="background-color: #F3F4F6;">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center" style="color: #4B5EAA;">Вход для специалистов</h2>

            @if (session('phone_step'))
                <form method="POST" action="{{ route('specialist.verify-code') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700" style="color: #6B7280;">Код из SMS</label>
                        <input type="text" name="code" id="code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Введите код">
                        @error('code')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700" style="background-color: #4B5EAA;">Подтвердить код</button>
                </form>
                <p class="mt-4 text-sm text-gray-600 text-center" style="color: #6B7280;">Код для тестирования: 123456</p>
            @else
                <form method="POST" action="{{ route('specialist.login') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700" style="color: #6B7280;">Номер телефона</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="+79991234567">
                        @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700" style="background-color: #4B5EAA;">Получить код</button>
                </form>
            @endif

            <p class="mt-4 text-sm text-gray-600 text-center" style="color: #6B7280;">
                Нет аккаунта? <a href="{{ route('specialist.register') }}" class="text-indigo-600 hover:text-indigo-800">Зарегистрироваться</a>
            </p>
        </div>
    </div>
@endsection
