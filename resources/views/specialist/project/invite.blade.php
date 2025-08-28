@extends('layouts.specialist')
@section('title', 'Пригласить специалиста - YourOrd')
@section('content')
    <div class="max-w-md mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Пригласить специалиста в {{ $project->name }}</h1>
        <form method="POST" action="{{ route('specialist.project.invite', $project->slug) }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email специалиста</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Права</label>
                <label><input type="checkbox" name="permissions[]" value="manage_schedule"> Управление расписанием</label>
                <label><input type="checkbox" name="permissions[]" value="view_schedule"> Просмотр расписания</label>
                <label><input type="checkbox" name="permissions[]" value="manage_balance"> Управление балансом</label>
                <label><input type="checkbox" name="permissions[]" value="manage_specialists"> Управление специалистами</label>
                <label><input type="checkbox" name="permissions[]" value="confirm_bookings"> Подтверждение записей</label>
                <label><input type="checkbox" name="permissions[]" value="manual_bookings"> Ручное добавление записей</label>
                <label><input type="checkbox" name="permissions[]" value="manage_services"> Управление услугами</label>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Пригласить</button>
        </form>
    </div>
@endsection
