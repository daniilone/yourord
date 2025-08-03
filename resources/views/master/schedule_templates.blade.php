<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шаблоны расписания - YourOrd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Ваши шаблоны расписания</h1>
    <nav class="nav mb-3">
        <a class="nav-link" href="{{ route('master.dashboard') }}">Назад в кабинет</a>
        <a class="nav-link" href="{{ route('master.projects') }}">Проекты</a>
        <a class="nav-link" href="{{ route('master.categories') }}">Категории</a>
        <a class="nav-link" href="{{ route('master.services') }}">Услуги</a>
        <a class="nav-link" href="{{ route('master.daily_schedules') }}">Расписание</a>
        <a class="nav-link" href="{{ route('master.bookings') }}">Записи</a>
        <a class="nav-link" href="{{ route('master.blacklist') }}">Черный список</a>
        <a class="nav-link" href="{{ route('master.auth.logout') }}">Выйти</a>
    </nav>

    <h2>Список шаблонов</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Проект</th>
            <th>Название</th>
            <th>Рабочий день</th>
            <th>Время</th>
            <th>Перерывы</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($templates as $template)
            <tr>
                <td>{{ $template->project->name }}</td>
                <td>{{ $template->name }}</td>
                <td>{{ $template->is_working_day ? 'Да' : 'Нет' }}</td>
                <td>{{ $template->is_working_day ? ($template->start_time . ' - ' . $template->end_time) : '-' }}</td>
                <td>
                    @foreach ($template->breaks as $break)
                        {{ $break->start_time }} - {{ $break->end_time }}<br>
                    @endforeach
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Добавить шаблон расписания</h2>
    <form method="POST" action="{{ route('master.daily_schedule_templates.create') }}">
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
            <label for="name" class="form-label">Название шаблона</label>
            <input type="text" name="name" id="name" class="form-control" required>
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_working_day" id="is_working_day" class="form-check-input" value="1" checked>
            <label for="is_working_day" class="form-check-label">Рабочий день</label>
        </div>
        <div id="working-hours">
            <div class="mb-3">
                <label for="start_time" class="form-label">Время начала</label>
                <input type="time" name="start_time" id="start_time" class="form-control" required>
                @error('start_time')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="end_time" class="form-label">Время окончания</label>
                <input type="time" name="end_time" id="end_time" class="form-control" required>
                @error('end_time')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div id="breaks-container">
                <h3>Перерывы</h3>
                <div class="break-row">
                    <input type="time" name="breaks[0][start_time]" class="form-control d-inline-block w-auto">
                    <input type="time" name="breaks[0][end_time]" class="form-control d-inline-block w-auto">
                    <button type="button" class="btn btn-danger btn-sm remove-break">Удалить</button>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mt-2" id="add-break">Добавить перерыв</button>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Сохранить шаблон</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('is_working_day').addEventListener('change', function () {
        document.getElementById('working-hours').style.display = this.checked ? 'block' : 'none';
    });

    let breakIndex = 1;
    document.getElementById('add-break').addEventListener('click', function () {
        const container = document.getElementById('breaks-container');
        const newBreak = document.createElement('div');
        newBreak.className = 'break-row';
        newBreak.innerHTML = `
                <input type="time" name="breaks[${breakIndex}][start_time]" class="form-control d-inline-block w-auto" required>
                <input type="time" name="breaks[${breakIndex}][end_time]" class="form-control d-inline-block w-auto" required>
                <button type="button" class="btn btn-danger btn-sm remove-break">Удалить</button>
            `;
        container.appendChild(newBreak);
        breakIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-break')) {
            e.target.parentElement.remove();
        }
    });
</script>
</body>
</html>
