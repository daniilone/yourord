@extends('layouts.client')
@section('title', 'Подтверждение кода - Клиент')
@section('content')
    <div class="max-w-md mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Введите код из SMS</h1>
        <form method="POST" action="{{ route('client.auth.verify') }}" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700">Код</label>
                <input type="text" name="code" id="code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('code')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Подтвердить</button>
        </form>
    </div>
@endsection
