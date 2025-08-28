@extends('layouts.specialist')
@section('title', 'Приглашение в проект - YourOrd')
@section('content')
    <div class="max-w-md mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Приглашение в проект: {{ $invitation->project->name }}</h1>
        <p class="mb-4">Вы приглашены в проект "{{ $invitation->project->name }}".</p>
        <p class="mb-4">Права:</p>
        <ul class="list-disc pl-5 mb-4">
            @foreach ($invitation->permissions ?? [] as $permission)
                <li>
                    @if ($permission == 'manage_schedule')
                        Управление расписанием
                    @elseif ($permission == 'view_schedule')
                        Просмотр расписания
                    @elseif ($permission == 'manage_balance')
                        Управление балансом
                    @elseif ($permission == 'manage_specialists')
                        Управление специалистами
                    @elseif ($permission == 'confirm_bookings')
                        Подтверждение записей
                    @elseif ($permission == 'manual_bookings')
                        Ручное добавление записей
                    @elseif ($permission == 'manage_services')
                        Управление услугами
                    @endif
                </li>
            @endforeach
        </ul>
        <form method="POST" action="{{ route('specialist.invitation.accept', $invitation->token) }}">
            @csrf
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Принять</button>
        </form>
    </div>
@endsection
