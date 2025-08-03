<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Услуги - YourOrd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Ваши услуги</h1>
    <nav class="nav mb-3">
        <a class="nav-link" href="{{ route('master.dashboard') }}">Назад в кабинет</a>
        <a class="nav-link" href="{{ route('master.projects') }}">Проекты</a>
        <a class="nav-link" href="{{ route('master.categories') }}">Категории</a>
        <a class="nav-link" href="{{ route('master.daily_schedules') }}">Расписание</a>
        <a class="nav-link" href="{{ route('master.daily_schedule_templates') }}">Шаблоны расписания</a>
        <a class="nav-link" href="{{ route('master.bookings') }}">Записи</a>
        <a class="nav-link" href="{{ route('master.blacklist') }}">Черный список</a>
        <a class="nav-link" href="{{ route('master.auth.logout') }}">Выйти</a>
    </nav>

    <h2>Список услуг</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Проект</th>
            <th>Категория</th>
            <th>Название</th>
            <th>Длительность (мин)</th>
            <th>Цена</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($services as $service)
            <tr>
                <td>{{ $service->project->name }}</td>
                <td>{{ $service->category->name }}</td>
                <td>{{ $service->name }}</td>
                <td>{{ $service->duration }}</td>
                <td>{{ $service->price }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Добавить услугу</h2>
    <form method="POST" action="{{ route('master.services.create') }}" class="mt-3">
        @csrf
        <div class="mb-3">
            <label for="project_id" class="form-label">Проект</label>
            <select name="project_id" id="project_id" class="form-control" required>
                @foreach (App\Models\Project::where('master_id', Auth::guard('master')->id())->get() as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
            @error('project_id')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Категория</label>
            <select name="category_id" id="category_id" class="form-control" required>
                @foreach (App\Models\Category::whereIn('project_id', App\Models\Project::where('master_id', Auth::guard('master')->id())->pluck('id'))->get() as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input type="text" name="name" id="name" class="form-control" required>
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Длительность (минуты)</label>
            <input type="number" name="duration" id="duration" class="form-control" required min="1">
            @error('duration')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Цена</label>
            <input type="number" name="price" id="price" class="form-control" required min="0" step="0.01">
            @error('price')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Создать услугу</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
