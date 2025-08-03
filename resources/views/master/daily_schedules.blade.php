<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание - YourOrd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        #calendar {
            max-width: 900px;
            margin: 20px auto;
        }
        .break-row {
            margin-bottom: 10px;
        }
        .break-row input {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Ваше расписание</h1>
    <nav class="nav mb-3">
        <a class="nav-link" href="{{ route('master.dashboard') }}">Назад в кабинет</a>
        <a class="nav-link" href="{{ route('master.projects') }}">Проекты</a>
        <a class="nav-link" href="{{ route('master.categories') }}">Категории</a>
        <a class="nav-link" href="{{ route('master.services') }}">Услуги</a>
        <a class="nav-link" href="{{ route('master.daily_schedule_templates') }}">Шаблоны расписания</a>
        <a class="nav-link" href="{{ route('master.bookings') }}">Записи</a>
        <a class="nav-link" href="{{ route('master.blacklist') }}">Черный список</a>
        <a class="nav-link" href="{{ route('master.auth.logout') }}">Выйти</a>
    </nav>

    <h2>Календарь расписания</h2>
    <div id="calendar"></div>

    <h2 class="mt-5">Добавить расписание на день</h2>
    <form method="POST" action="{{ route('master.daily_schedules.create') }}" id="schedule-form" class="mt-3">
        @csrf
        <div class="mb-3">
            <label for="create_project_id" class="form-label">Проект</label>
            <select name="project_id" id="create_project_id" class="form-control" required>
                @foreach (App\Models\Project::where('master_id', Auth::guard('master')->id())->get() as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
            @error('project_id')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="create_date" class="form-label">Дата</label>
            <input type="date" name="date" id="create_date" class="form-control" required>
            @error('date')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_working_day" id="create_is_working_day" class="form-check-input" value="1" checked>
            <label for="create_is_working_day" class="form-check-label">Рабочий день</label>
        </div>
        <div id="create_working_hours" style="display: block;">
            <div class="mb-3">
                <label for="create_start_time" class="form-label">Время начала</label>
                <input type="time" name="start_time" id="create_start_time" class="form-control" required>
                @error('start_time')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="create_end_time" class="form-label">Время окончания</label>
                <input type="time" name="end_time" id="create_end_time" class="form-control" required>
                @error('end_time')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div id="create_breaks_container">
                <h3>Перерывы</h3>
                <div class="break-row">
                    <input type="time" name="breaks[0][start_time]" class="form-control d-inline-block w-auto">
                    <input type="time" name="breaks[0][end_time]" class="form-control d-inline-block w-auto">
                    <button type="button" class="btn btn-danger btn-sm remove-break">Удалить</button>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mt-2" id="create_add_break">Добавить перерыв</button>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Сохранить расписание</button>
    </form>

    <h2 class="mt-5">Редактировать расписание</h2>
    <form method="POST" id="edit-schedule-form" class="mt-3" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="schedule_id" id="edit_schedule_id">
        <div class="mb-3">
            <label for="edit_project_id" class="form-label">Проект</label>
            <select name="project_id" id="edit_project_id" class="form-control" required>
                @foreach (App\Models\Project::where('master_id', Auth::guard('master')->id())->get() as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
            @error('project_id')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edit_date" class="form-label">Дата</label>
            <input type="date" name="date" id="edit_date" class="form-control" required>
            @error('date')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_working_day" id="edit_is_working_day" class="form-check-input" value="1">
            <label for="edit_is_working_day" class="form-check-label">Рабочий день</label>
        </div>
        <div id="edit_working_hours">
            <div class="mb-3">
                <label for="edit_start_time" class="form-label">Время начала</label>
                <input type="time" name="start_time" id="edit_start_time" class="form-control">
                @error('start_time')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="edit_end_time" class="form-label">Время окончания</label>
                <input type="time" name="end_time" id="edit_end_time" class="form-control">
                @error('end_time')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div id="edit_breaks_container">
                <h3>Перерывы</h3>
            </div>
            <button type="button" class="btn btn-secondary mt-2" id="edit_add_break">Добавить перерыв</button>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Обновить расписание</button>
        <button type="button" class="btn btn-secondary mt-3" id="cancel_edit">Отменить</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ru',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                    @foreach ($schedules as $schedule)
                {
                    title: '{{ $schedule->is_working_day ? "Рабочий день" : "Выходной" }}',
                    start: '{{ \Carbon\Carbon::parse($schedule->date)->format('Y-m-d') }}', // Используем Carbon для парсинга строки
                    color: '{{ $schedule->is_working_day ? "#28a745" : "#dc3545" }}',
                    extendedProps: {
                        schedule_id: {{ $schedule->id }},
                        project_id: {{ $schedule->project_id }},
                        start_time: '{{ $schedule->start_time }}',
                        end_time: '{{ $schedule->end_time }}',
                        breaks: @json($schedule->workBreaks->map(fn($break) => ['start_time' => $break->start_time, 'end_time' => $break->end_time]))
                    }
                },
                @endforeach
            ],
            eventClick: function (info) {
                var event = info.event;
                var form = document.getElementById('edit-schedule-form');
                var action = '{{ route("master.daily_schedules.update", ":id") }}'.replace(':id', event.extendedProps.schedule_id);
                form.action = action;
                form.style.display = 'block';
                document.getElementById('schedule-form').style.display = 'none';

                document.getElementById('edit_schedule_id').value = event.extendedProps.schedule_id;
                document.getElementById('edit_project_id').value = event.extendedProps.project_id;
                document.getElementById('edit_date').value = event.startStr;
                document.getElementById('edit_is_working_day').checked = event.title === 'Рабочий день';
                document.getElementById('edit_working_hours').style.display = event.title === 'Рабочий день' ? 'block' : 'none';
                document.getElementById('edit_start_time').value = event.extendedProps.start_time || '';
                document.getElementById('edit_end_time').value = event.extendedProps.end_time || '';

                var breaksContainer = document.getElementById('edit_breaks_container');
                breaksContainer.innerHTML = '<h3>Перерывы</h3>';
                event.extendedProps.breaks.forEach((b, index) => {
                    breaksContainer.innerHTML += `
                            <div class="break-row">
                                <input type="time" name="breaks[${index}][start_time]" class="form-control d-inline-block w-auto" value="${b.start_time}">
                                <input type="time" name="breaks[${index}][end_time]" class="form-control d-inline-block w-auto" value="${b.end_time}">
                                <button type="button" class="btn btn-danger btn-sm remove-break">Удалить</button>
                            </div>
                        `;
                });
                form.scrollIntoView();
            },
            dateClick: function (info) {
                document.getElementById('create_date').value = info.dateStr;
                document.getElementById('schedule-form').style.display = 'block';
                document.getElementById('edit-schedule-form').style.display = 'none';
                document.getElementById('schedule-form').scrollIntoView();
            }
        });
        calendar.render();

        document.getElementById('create_is_working_day').addEventListener('change', function () {
            document.getElementById('create_working_hours').style.display = this.checked ? 'block' : 'none';
        });

        document.getElementById('edit_is_working_day').addEventListener('change', function () {
            document.getElementById('edit_working_hours').style.display = this.checked ? 'block' : 'none';
        });

        let createBreakIndex = 1;
        document.getElementById('create_add_break').addEventListener('click', function () {
            const container = document.getElementById('create_breaks_container');
            const newBreak = document.createElement('div');
            newBreak.className = 'break-row';
            newBreak.innerHTML = `
                    <div class="break-row">
                        <input type="time" name="breaks[${createBreakIndex}][start_time]" class="form-control d-inline-block w-auto" required>
                        <input type="time" name="breaks[${createBreakIndex}][end_time]" class="form-control d-inline-block w-auto" required>
                        <button type="button" class="btn btn-danger btn-sm remove-break">Удалить</button>
                    </div>
                `;
            container.appendChild(newBreak);
            createBreakIndex++;
        });

        let editBreakIndex = document.querySelectorAll('#edit_breaks_container .break-row').length;
        document.getElementById('edit_add_break').addEventListener('click', function () {
            const container = document.getElementById('edit_breaks_container');
            const newBreak = document.createElement('div');
            newBreak.className = 'break-row';
            newBreak.innerHTML = `
                    <div class="break-row">
                        <input type="time" name="breaks[${editBreakIndex}][start_time]" class="form-control d-inline-block w-auto" required>
                        <input type="time" name="breaks[${editBreakIndex}][end_time]" class="form-control d-inline-block w-auto" required>
                        <button type="button" class="btn btn-danger btn-sm remove-break">Удалить</button>
                    </div>
                `;
            container.appendChild(newBreak);
            editBreakIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-break')) {
                e.target.parentElement.remove();
            }
        });

        document.getElementById('cancel_edit').addEventListener('click', function () {
            document.getElementById('edit-schedule-form').style.display = 'none';
            document.getElementById('schedule-form').style.display = 'block';
        });
    });
</script>
</body>
</html>
