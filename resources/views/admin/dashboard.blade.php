<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - YourOrd</title>
</head>
<body>
<h1>Админ-панель</h1>
<p>Добро пожаловать, {{ auth()->guard('web')->user()->email }}</p>
<h2>Статистика</h2>
<ul>
    <li>Администраторы: {{ $stats['users'] }}</li>
    <li>Клиенты: {{ $stats['clients'] }}</li>
    <li>Мастера: {{ $stats['masters'] }}</li>
    <li>Проекты: {{ $stats['projects'] }}</li>
    <li>Записи: {{ $stats['bookings'] }}</li>
    <li>Тарифы: {{ $stats['tariffs'] }}</li>
    <li>Платежи: {{ $stats['payments'] }}</li>
</ul>
<form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button type="submit">Выйти</button>
</form>
</body>
</html>
