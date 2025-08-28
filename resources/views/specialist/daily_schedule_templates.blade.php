<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шаблоны расписания - YourOrd</title>
</head>
<body>
<h1>Шаблоны расписания</h1>
<a href="{{ route('master.dashboard') }}">Назад в кабинет</a>
<a href="{{ route('master.daily_schedules') }}">Расписание</a>
<a href="{{ route('master.auth.logout') }}">Выйти</a>

<h2>Ваши шаблоны</h2>
<table>
    <thead>
    <tr>
        <th>Проект</th>
        <th>Название</th>
        <th>Рабочий день</th>
        <th>Рабочее время</th>
        <th>Перерывы</th>
        <th>Действия</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($templates as $template)
        <tr>
            <td>{{ $template->project->name }}</td>
            <td>{{ $template->name }}</td>
            <td>{{ $template->is_working_day ? 'Да' : 'Нет' }}</td>
            <td>
                @if ($template->is_working_day)
                    {{ $template->start_time }} - {{ $template->end_time }}
                @else
                    -
                @endif
            </td>
            <td>
                @foreach ($template->breaks as $break)
                    {{ $break->start_time }} - {{ $break->end_time }}<br>
                @endforeach
            </td>
            <td>
                <form method="POST" action="{{ route('master.daily_schedule_templates.apply') }}">
                    @csrf
                    <input type="hidden" name="template_id" value="{{ $template->id }}">
                    <label for="date_{{ $template->id }}">Дата</label>
                    <input type="date" name="date" id="date_{{ $template->id }}" required>
                    <button type="submit">Применить</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Создать шаблон</h2>
<form method="POST" action="{{ route('master.daily_schedule_templates.create') }}">
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
        <label for="name">Название шаблона</label>
        <input type="text" name="name" id="name" required>
    </div>
    <div>
        <label for="is_working_day">Рабочий день</label>
        <input type="checkbox" name="is_working_day" id="is_working_day" value="1" checked>
    </div>
    <div id="working_hours" style="display: block;">
        <div>
            <label for="start_time">Начало рабочего времени</label>
            <input type="time" name="start_time" id="start_time">
        </div>
        <div>
            <label for="end_time">Конец рабочего времени</label>
            <input type="time" name="end_time" id="end_time">
        </div>
        <div id="breaks">
            <div class="break">
                <label>Перерыв</label>
                <input type="time" name="breaks[0][start_time]">
                <input type="time" name="breaks[0][end_time]">
            </div>
        </div>
        <button type="button" onclick="addBreak()">Добавить перерыв</button>
    </div>
    <button type="submit">Создать шаблон</button>
</form>

<script>
    document.getElementById('is_working_day').addEventListener('change', function () {
        document.getElementById('working_hours').style.display = this.checked ? 'block' : 'none';
    });

    let breakIndex = 1;
    function addBreak() {
        const breaksDiv = document.getElementById('breaks');
        const newBreak = document.createElement('div');
        newBreak.className = 'break';
        newBreak.innerHTML = `
                <label>Перерыв</label>
                <input type="time" name="breaks[${breakIndex}][start_time]">
                <input type="time" name="breaks[${breakIndex}][end_time]">
            `;
        breaksDiv.appendChild(newBreak);
        breakIndex++;
    }
</script>
</body>
</html>
