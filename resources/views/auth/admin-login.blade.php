<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход администратора - YourOrd</title>
</head>
<body>
<h1>Вход администратора</h1>
<form method="POST" action="{{ route('admin.login') }}">
    @csrf
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
    </div>
    <div>
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" required>
    </div>
    <button type="submit">Войти</button>
    @if ($errors->has('email'))
        <p style="color: red;">{{ $errors->first('email') }}</p>
    @endif
</form>
</body>
</html>
