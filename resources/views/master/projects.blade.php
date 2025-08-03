<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проекты - YourOrd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Ваши проекты</h1>
    <nav class="nav mb-3">
        <a class="nav-link" href="{{ route('master.dashboard') }}">Назад в кабинет</a>
        <a class="nav-link" href="{{ route('master.categories') }}">Категории</a>
        <a class="nav-link" href="{{ route('master.services') }}">Услуги</a>
        <a class="nav-link" href="{{ route('master.daily_schedules') }}">Расписание</a>
        <a class="nav-link" href="{{ route('master.daily_schedule_templates') }}">Шаблоны расписания</a>
        <a class="nav-link" href="{{ route('master.bookings') }}">Записи</a>
        <a class="nav-link" href="{{ route('master.blacklist') }}">Черный список</a>
        <a class="nav-link" href="{{ route('master.auth.logout') }}">Выйти</a>
    </nav>

    <h2>Список проектов</h2>
    <table class="table table-striped">
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

    <h2>Добавить проект</h2>
    <form method="POST" action="{{ route('master.projects.create') }}" class="mt-3">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input type="text" name="name" id="name" class="form-control" required>
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Создать проект</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
