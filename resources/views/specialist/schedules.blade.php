<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание мастера - YourOrd</title>
</head>
<body>
<h1>Расписание</h1>
<a href="{{ route('master.dashboard') }}">Назад в кабинет</a>
<a href="{{ route('master.schedule_templates') }}">Шаблоны расписания</a>
<a href="{{ route('master.auth.logout') }}">Выйти</a>

<h2>Рабочие слоты</h2>
<table>
    <thead>
    <tr>
        <th>Проект</th>
        <th>Начало</th>
        <th>Конец</th>
        <th>Занят</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($schedules as $schedule)
        <tr>
            <td>{{ $schedule->project->name }}</td>
            <td>{{ $schedule->start_time->format('d.m.Y H:i') }}</td>
            <td>{{ $schedule->end_time->format('d.m.Y H:i') }}</td>
            <td>{{ $schedule->is_booked ? 'Да' : 'Нет' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Перерывы</h2>
<table>
    <thead>
    <tr>
        <th>Проект</th>
        <th>Начало</th>
        <th>Конец</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($breaks as $break)
        <tr>
            <td>{{ $break->project->name }}</td>
            <td>{{ $break->start_time->format('d.m.Y H:i') }}</td>
            <td>{{ $break->end_time->format('d.m.Y H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Выходные дни</h2>
<table>
    <thead>
    <tr>
        <th>Проект</th>
        <th>Дата</th>
        <th>Причина</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($daysOff as $dayOff)
        <tr>
            <td>{{ $dayOff->project->name }}</td>
            <td>{{ $dayOff->date->format('d.m.Y') }}</td>
            <td>{{ $dayOff->reason ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Добавить рабочий слот</h2>
<form method="POST" action="{{ route('master.schedules.create') }}">
    @csrf
    <div>
        <label for="project_id">Проект</label>
        <select name="project_id" id="project_id" required>
            @foreach (auth('master')->user()->projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="start_time">Начало</label>
        <input type="datetime-local" name="start_time" id="start_time" required>
    </div>
    <div>
        <label for="end_time">Конец</label>
        <input type="datetime-local" name="end_time" id="end_time" required>
    </div>
    <button type="submit">Добавить</button>
    @error('date')
    <p style="color: red;">{{ $message }}</p>
    @enderror
</form>

<h2>Добавить перерыв</h2>
<form method="POST" action="{{ route('master.breaks.create') }}">
    @csrf
    <div>
        <label for="project_id">Проект</label>
        <select name="project_id" id="project_id" required>
            @foreach (auth('master')->user()->projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="start_time">Начало</label>
        <input type="datetime-local" name="start_time" id="start_time" required>
    </div>
    <div>
        <label for="end_time">Конец</label>
        <input type="datetime-local" name="end_time" id="end_time" required>
    </div>
    <button type="submit">Добавить</button>
    @error('date')
    <p style="color: red;">{{ $message }}</p>
    @enderror
</form>

<h2>Добавить выходной день</h2>
<form method="POST" action="{{ route('master.days-off.create') }}">
    @csrf
    <div>
        <label for="project_id">Проект</label>
        <select name="project_id" id="project_id" required>
            @foreach (auth('master')->user()->projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="date">Дата</label>
        <input type="date" name="date" id="date" required>
    </div>
    <div>
        <label for="reason">Причина</label>
        <input type="text" name="reason" id="reason">
    </div>
    <button type="submit">Добавить</button>
    @error('date')
    <p style="color: red;">{{ $message }}</p>
    @enderror
</form>
</body>
</html>
