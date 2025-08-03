<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Записи мастера - YourOrd</title>
</head>
<body>
<h1>Записи</h1>
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
@forelse ($bookings as $booking)
    <p>{{ $booking->project->name }}: {{ $booking->service->name }} - {{ $booking->client_email ?? 'Аноним' }} - {{ $booking->schedule->start_time->format('d.m.Y H:i') }} ({{ $booking->status }})</p>
@empty
    <p>Записей нет</p>
@endforelse
<h2>Создать ручную запись</h2>
<form method="POST" action="{{ route('master.bookings.create') }}">
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
        <label for="service_id">Услуга</label>
        <select name="service_id" id="service_id" required>
            @foreach (\App\Models\Service::whereIn('project_id', auth()->guard('master')->user()->projects->pluck('id'))->get() as $service)
                <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration }} мин)</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="schedule_id">Время</label>
        <select name="schedule_id" id="schedule_id" required>
            @foreach (\App\Models\Schedule::where('master_id', auth()->guard('master')->id())->where('type', 'work')->get() as $schedule)
                <option value="{{ $schedule->id }}">{{ $schedule->start_time->format('d.m.Y H:i') }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="client_email">Email клиента</label>
        <input type="email" name="client_email" id="client_email" required>
    </div>
    <div>
        <label for="client_name">Имя клиента</label>
        <input type="text" name="client_name" id="client_name">
    </div>
    <button type="submit">Создать</button>
</form>
<a href="{{ route('master.dashboard') }}">Назад</a>
</body>
</html>
