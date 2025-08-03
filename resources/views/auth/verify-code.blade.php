<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подтверждение кода - YourOrd</title>
</head>
<body>
<h1>Подтверждение кода</h1>
<form method="POST" action="{{ route('auth.verify-code') }}">
    @csrf
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ session('email') }}" required>
    </div>
    <div>
        <label for="code">Код</label>
        <input type="text" name="code" id="code" required>
    </div>
    <div>
        <label for="user_type">Тип пользователя</label>
        <select name="user_type" id="user_type" required>
            <option value="client" {{ session('user_type') == 'client' ? 'selected' : '' }}>Клиент</option>
            <option value="master" {{ session('user_type') == 'master' ? 'selected' : '' }}>Мастер</option>
        </select>
    </div>
    <button type="submit">Войти</button>
    @if ($errors->has('code'))
        <p style="color: red;">{{ $errors->first('code') }}</p>
    @endif
</form>
</body>
</html>
