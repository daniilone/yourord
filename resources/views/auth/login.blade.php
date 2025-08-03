<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - YourOrd</title>
</head>
<body>
<h1>Вход</h1>
<form method="POST" action="{{ route('auth.send-code') }}">
    @csrf
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
    </div>
    <div>
        <label for="user_type">Тип пользователя</label>
        <select name="user_type" id="user_type" required>
            <option value="client">Клиент</option>
            <option value="master">Мастер</option>
        </select>
    </div>
    <button type="submit">Отправить код</button>
    @if (session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif
</form>
<a href="{{ route('admin.login') }}">Вход для администратора</a>
</body>
</html>
