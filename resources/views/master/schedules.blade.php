<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание мастера - YourOrd</title>
</head>
<body>
<h1>Расписание</h1>
@if (session('message'))
    <p style="color: green;">{{ session('message') }}</p>
@endif
@if ($errors->any())
    <ul style="color: red;">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif
@forelse ($schedules as $schedule)
    <p>{{ $schedule->project->name }}: {{ $schedule->start_time->format('d.m.Y H:i') }} - {{ $schedule->end_time->format('d.m.Y H:i') }} ({{ $schedule->type }})</p>
@empty
    <p>Слотов нет</p>
@endforelse
<h2>Добавить слот</h2>
<form method="POST" action="{{ route('master.schedules.store') }}">
    @csrf
    <div>
        <label for="project_id">Проект</label>
        <select name="project_id" id="project_id" required>
            @foreach (auth()->guard('master')->user()->projects as $project)
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
    <div>
        <label for="type">Тип</label>
        <select name="type" id="type" required>
            <option value="work">Рабочий слот</option>
            <option value="break">Перерыв</option>
            <option value="day_off">Выходной</option>
        </select>
    </div>
    <div>
        <label for="floating_break_buffer">Буфер перерыва (минуты)</label>
        <input type="number" name="floating_break_buffer" id="floating_break_buffer" min="0">
    </div>
    <button type="submit">Добавить</button>
</form>
<a href="{{ route('master.dashboard') }}">Назад</a>
</body>
</html>
