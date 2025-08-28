@component('mail::message')
    # Приглашение в проект

    Вы приглашены в проект "{{ $invitation->project->name }}".

    **Права:**
    @foreach ($invitation->permissions ?? [] as $permission)
        - @if ($permission == 'manage_schedule')
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
    @endforeach

    @component('mail::button', ['url' => $url])
        Принять приглашение
    @endcomponent

    Спасибо,
    YourOrd
@endcomponent
