<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет мастера - YourOrd</title>
</head>
<body>
<h1>Кабинет мастера</h1>
<p>Добро пожаловать, {{ auth()->guard('master')->user()->email }}</p>
<a href="{{ route('master.bookings') }}">Мои записи</a>
<a href="{{ route('master.schedules') }}">Расписание</a>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Выйти</button>
</form>
</body>
</html>
