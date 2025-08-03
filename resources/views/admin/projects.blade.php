<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проекты - YourOrd</title>
</head>
<body>
<h1>Проекты</h1>
<a href="{{ route('admin.dashboard') }}">Назад в кабинет</a>
<a href="{{ route('admin.auth.logout') }}">Выйти</a>

<table>
    <thead>
    <tr>
        <th>Мастер</th>
        <th>Название</th>
        <th>Описание</th>
        <th>Категории</th>
        <th>Расписание</th>
        <th>Шаблоны расписания</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($projects as $project)
        <tr>
            <td>{{ $project->master->name ?? $project->master->email }}</td>
            <td>{{ $project->name }}</td>
            <td>{{ $project->description ?? '-' }}</td>
            <td>
                @foreach ($project->categories as $category)
                    {{ $category->name }}<br>
                @endforeach
            </td>
            <td>
                @foreach ($project->dailySchedules as $schedule)
                    {{ $schedule->date->format('d.m.Y') }}: {{ $schedule->is_working_day ? 'Рабочий' : 'Выходной' }}
                    @if ($schedule->is_working_day)
                        ({{ $schedule->start_time }} - {{ $schedule->end_time }})
                        @foreach ($schedule->workBreaks as $break)
                            Перерыв: {{ $break->start_time }} - {{ $break->end_time }}<br>
                        @endforeach
                    @endif
                    <hr>
                @endforeach
            </td>
            <td>
                @foreach ($project->dailyScheduleTemplates as $template)
                    {{ $template->name }}: {{ $template->is_working_day ? 'Рабочий' : 'Выходной' }}
                    @if ($template->is_working_day)
                        ({{ $template->start_time }} - {{ $template->end_time }})
                        @foreach ($template->breaks as $break)
                            Перерыв: {{ $break->start_time }} - {{ $break->end_time }}<br>
                        @endforeach
                    @endif
                    <hr>
                @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
