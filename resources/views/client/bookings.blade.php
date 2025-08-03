<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои записи - YourOrd</title>
</head>
<body>
<h1>Мои записи</h1>
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
@foreach ($bookings as $booking)
    <p>{{ $booking->service->name }} - {{ $booking->schedule->start_time->format('d.m.Y H:i') }} ({{ $booking->status }})</p>
@endforeach
<h2>Новая запись</h2>
<form method="POST" action="{{ route('booking.create') }}">
    @csrf
    <div>
        <label for="project_id">Проект</label>
        <select name="project_id" id="project_id" required>
            @foreach (\App\Models\Project::all() as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="service_id">Услуга</label>
        <select name="service_id" id="service_id" required>
            @foreach (\App\Models\Service::all() as $service)
                <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration }} мин)</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="schedule_id">Время</label>
        <select name="schedule_id" id="schedule_id" required>
            @foreach (\App\Models\Schedule::where('type', 'work')->get() as $schedule)
                <option value="{{ $schedule->id }}">{{ $schedule->start_time->format('d.m.Y H:i') }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit">Записаться</button>
</form>
<a href="{{ route('client.dashboard') }}">Назад</a>
</body>
</html>
