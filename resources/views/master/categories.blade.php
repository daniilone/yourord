<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Категории - YourOrd</title>
</head>
<body>
<h1>Ваши категории</h1>
<nav>
    <a href="{{ route('master.dashboard') }}">Назад в кабинет</a>
    <a href="{{ route('master.projects') }}">Проекты</a>
    <a href="{{ route('master.services') }}">Услуги</a>
    <a href="{{ route('master.daily_schedules') }}">Расписание</a>
    <a href="{{ route('master.daily_schedule_templates') }}">Шаблоны расписания</a>
    <a href="{{ route('master.bookings') }}">Записи</a>
    <a href="{{ route('master.blacklist') }}">Черный список</a>
    <a href="{{ route('master.auth.logout') }}">Выйти</a>
</nav>

<h2>Список категорий</h2>
<table>
    <thead>
    <tr>
        <th>Проект</th>
        <th>Название</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($categories as $category)
        <tr>
            <td>{{ $category->project->name }}</td>
            <td>{{ $category->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Добавить категорию</h2>
<form method="POST" action="{{ route('master.categories.create') }}">
    @csrf
    <div>
        <label for="project_id">Проект</label>
        <select name="project_id" id="project_id" required>
            @foreach (App\Models\Project::where('master_id', Auth::guard('master')->id())->get() as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="name">Название</label>
        <input type="text" name="name" id="name" required>
    </div>
    <button type="submit">Создать категорию</button>
    @error('name')
    <p style="color: red;">{{ $message }}</p>
    @enderror
    @error('project_id')
    <p style="color: red;">{{ $message }}</p>
    @enderror
</form>
</body>
</html>
