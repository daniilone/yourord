<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои проекты - YourOrd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Мои проекты</h1>
    <nav class="nav mb-3">
        <a class="nav-link" href="{{ route('client.dashboard') }}">Назад в кабинет</a>
        <a class="nav-link" href="{{ route('client.bookings') }}">Мои записи</a>
        <a class="nav-link" href="{{ route('client.auth.logout') }}">Выйти</a>
    </nav>

    <h2>Список избранных проектов</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Название</th>
            <th>Описание</th>
            <th>Мастер</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($projects as $project)
            <tr>
                <td>{{ $project->name }}</td>
                <td>{{ $project->description ?? '-' }}</td>
                <td>{{ $project->master->name ?? $project->master->email }}</td>
                <td>
                    <a href="{{ route('client.project', $project->slug) }}" class="btn btn-primary btn-sm">Просмотреть</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
