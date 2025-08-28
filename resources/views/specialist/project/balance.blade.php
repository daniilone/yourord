@extends('layouts.specialist')
@section('title', 'Пополнить баланс - ' . $project->name)
@section('content')
    <div class="max-w-md mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Пополнить баланс: {{ $project->name }}</h1>
        <form method="POST" action="{{ route('specialist.project.balance', $project->slug) }}" class="space-y-4">
            @csrf
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Сумма (руб.)</label>
                <input type="number" name="amount" id="amount" value="{{ old('amount') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('amount')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Пополнить</button>
        </form>
    </div>
@endsection
