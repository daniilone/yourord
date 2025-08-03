<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои записи - YourOrd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Мои записи</h1>
    <nav class="nav mb-3">
        <a class="nav-link" href="{{ route('client.dashboard') }}">Назад в кабинет</a>
        <a class="nav-link" href="{{ route('client.projects') }}">Мои проекты</a>
        <a class="nav-link" href="{{ route('client.auth.logout') }}">Выйти</a>
    </nav>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Проект</th>
            <th>Услуга</th>
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
                <td>{{ is_string($booking->dailySchedule->date) ? \Carbon\Carbon::parse($booking->dailySchedule->date)->format('d.m.Y') : $booking->dailySchedule->date->format('d.m.Y') }}</td>
                <td>{{ $booking->start_time }}</td>
                <td>{{ $booking->status }}</td>
                <td>
                    @if ($booking->status != 'cancelled')
                        <form method="POST" action="{{ route('client.bookings.cancel', $booking) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-sm">Отменить</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
