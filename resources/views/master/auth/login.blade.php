<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход мастера - YourOrd</title>
</head>
<body>
<h1>Вход мастера</h1>
<form method="POST" action="{{ route('master.auth.send-code') }}">
    @csrf
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
    </div>
    <button type="submit">Отправить код</button>
    @if (session('message'))
        <p style="color: green;">{{ session('message') }}</p>
    @endif
    @error('email')
    <p style="color: red;">{{ $message }}</p>
    @enderror
</form>
</body>
</html>
