<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мастер - Дашборд</title>
</head>
<body>
<h1>Добро пожаловать, {{ $master->name ?? $master->email }}</h1>
<h2>Ваши проекты</h2>
<table>
    <thead>
    <tr>
        <th>Название</th>
        <th>Описание</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($projects as $project)
        <tr>
            <td>{{ $project->name }}</td>
            <td>{{ $project->description ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<a href="{{ route('master.projects') }}">Управление проектами</a>
<a href="{{ route('master.categories') }}">Категории</a>
<a href="{{ route('master.services') }}">Услуги</a>
<a href="{{ route('master.daily_schedules') }}">Расписание</a>
<a href="{{ route('master.daily_schedule_templates') }}">Шаблоны расписания</a>
<a href="{{ route('master.bookings') }}">Записи</a>
<a href="{{ route('master.blacklist') }}">Черный список</a>
<a href="{{ route('master.auth.logout') }}">Выйти</a>
</body>
</html>
