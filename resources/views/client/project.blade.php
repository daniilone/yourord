<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }} - YourOrd</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .time-slot {
            cursor: pointer;
            margin: 5px;
        }
        .time-slot:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>{{ $project->name }}</h1>
    <nav class="nav mb-3">
        <a class="nav-link" href="{{ route('client.dashboard') }}">Назад в кабинет</a>
        <a class="nav-link" href="{{ route('client.projects') }}">Проекты</a>
        <a class="nav-link" href="{{ route('client.bookings') }}">Мои записи</a>
        <a class="nav-link" href="{{ route('client.auth.logout') }}">Выйти</a>
    </nav>

    <h2>Описание</h2>
    <p>{{ $project->description ?? 'Нет описания' }}</p>

    <form method="POST" action="{{ route('client.project.favorite', $project->slug) }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-primary">Добавить в избранное</button>
    </form>

    <h2>Категории и услуги</h2>
    @foreach ($categories as $category)
        <h3>{{ $category->name }}</h3>
        <ul>
            @foreach ($services->where('category_id', $category->id) as $service)
                <li>{{ $service->name }} ({{ $service->duration }} мин, {{ $service->price }} руб.)</li>
            @endforeach
        </ul>
    @endforeach

    <h2 class="mt-5">Записаться</h2>
    <form method="POST" action="{{ route('client.project.booking', $project->slug) }}" class="mt-3">
        @csrf
        <div class="mb-3">
            <label for="service_id" class="form-label">Услуга</label>
            <select name="service_id" id="service_id" class="form-control" required>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration }} мин, {{ $service->price }} руб.)</option>
                @endforeach
            </select>
            @error('service_id')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Дата</label>
            <select name="date" id="date" class="form-control" required>
                @foreach ($timeSlots as $date => $slots)
                    @if (!empty($slots))
                        <option value="{{ $date }}">{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</option>
                    @endif
                @endforeach
            </select>
            @error('date')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="start_time" class="form-label">Время</label>
            <select name="start_time" id="start_time" class="form-control" required>
                <!-- Время заполняется JavaScript -->
            </select>
            @error('start_time')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Записаться</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeSlots = @json($timeSlots);
        const dateSelect = document.getElementById('date');
        const timeSelect = document.getElementById('start_time');

        function updateTimeSlots() {
            const selectedDate = dateSelect.value;
            timeSelect.innerHTML = '<option value="">Выберите время</option>';
            if (timeSlots[selectedDate]) {
                timeSlots[selectedDate].forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSelect.appendChild(option);
                });
            }
        }

        dateSelect.addEventListener('change', updateTimeSlots);
        if (dateSelect.value) {
            updateTimeSlots();
        }
    });
</script>
</body>
</html>
