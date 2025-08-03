<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Записи - YourOrd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Ваши записи</h1>
    <nav class="nav mb-3">
        <a class="nav-link" href="{{ route('master.dashboard') }}">Назад в кабинет</a>
        <a class="nav-link" href="{{ route('master.projects') }}">Проекты</a>
        <a class="nav-link" href="{{ route('master.categories') }}">Категории</a>
        <a class="nav-link" href="{{ route('master.services') }}">Услуги</a>
        <a class="nav-link" href="{{ route('master.daily_schedules') }}">Расписание</a>
        <a class="nav-link" href="{{ route('master.daily_schedule_templates') }}">Шаблоны расписания</a>
        <a class="nav-link" href="{{ route('master.blacklist') }}">Черный список</a>
        <a class="nav-link" href="{{ route('master.auth.logout') }}">Выйти</a>
    </nav>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Проект</th>
            <th>Услуга</th>
            <th>Клиент</th>
            <th>Дата</th>
            <th>Время</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($bookings as $booking)
            <tr>
                <td>{{ $booking->project->name }}</td>
                <td>{{ $booking->service->name }}</td>
                <td>{{ $booking->client->name ?? $booking->client_email }}</td>
                <td>{{ is_string($booking->dailySchedule->date) ? \Carbon\Carbon::parse($booking->dailySchedule->date)->format('d.m.Y') : $booking->dailySchedule->date->format('d.m.Y') }}</td>
                <td>{{ $booking->start_time }}</td>
                <td>{{ $booking->status }}</td>
                <td>
                    <form method="POST" action="{{ route('master.bookings.update', $booking) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select d-inline-block w-auto">
                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Ожидает</option>
                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Подтверждено</option>
                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Отменено</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Обновить</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
