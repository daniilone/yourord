@extends('layouts.specialist')
@section('title', 'Управление правами - ' . $project->name)
@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Управление правами для {{ $project->name }}</h1>
        <form method="POST" action="{{ route('specialist.project.permissions', $project->slug) }}">
            @csrf
            @foreach ($project->specialists as $specialist)
                @if (!$specialist->pivot->is_owner)
                    <div class="mb-4">
                        <h3 class="text-xl font-medium">{{ $specialist->name ?? $specialist->email }}</h3>
                        <div class="space-y-2">
                            <label><input type="checkbox" name="permissions[{{$specialist->id}}][]" value="manage_schedule" {{ in_array('manage_schedule', $specialist->pivot->permissions ?? []) ? 'checked' : '' }}> Управление расписанием</label>
                            <label><input type="checkbox" name="permissions[{{$specialist->id}}][]" value="view_schedule" {{ in_array('view_schedule', $specialist->pivot->permissions ?? []) ? 'checked' : '' }}> Просмотр расписания</label>
                            <label><input type="checkbox" name="permissions[{{$specialist->id}}][]" value="manage_balance" {{ in_array('manage_balance', $specialist->pivot->permissions ?? []) ? 'checked' : '' }}> Управление балансом</label>
                            <label><input type="checkbox" name="permissions[{{$specialist->id}}][]" value="manage_specialists" {{ in_array('manage_specialists', $specialist->pivot->permissions ?? []) ? 'checked' : '' }}> Управление специалистами</label>
                            <label><input type="checkbox" name="permissions[{{$specialist->id}}][]" value="confirm_bookings" {{ in_array('confirm_bookings', $specialist->pivot->permissions ?? []) ? 'checked' : '' }}> Подтверждение записей</label>
                            <label><input type="checkbox" name="permissions[{{$specialist->id}}][]" value="manual_bookings" {{ in_array('manual_bookings', $specialist->pivot->permissions ?? []) ? 'checked' : '' }}> Ручное добавление записей</label>
                            <label><input type="checkbox" name="permissions[{{$specialist->id}}][]" value="manage_services" {{ in_array('manage_services', $specialist->pivot->permissions ?? []) ? 'checked' : '' }}> Управление услугами</label>
                        </div>
                    </div>
                @endif
            @endforeach
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Сохранить</button>
        </form>
    </div>
@endsection
