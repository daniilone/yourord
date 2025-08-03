<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет клиента - YourOrd</title>
</head>
<body>
<h1>Кабинет клиента</h1>
<p>Добро пожаловать, {{ auth()->guard('client')->user()->email }}</p>
<a href="{{ route('client.bookings') }}">Мои записи</a>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Выйти</button>
</form>
</body>
</html>
