<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подтверждение кода - YourOrd</title>
</head>
<body>
<h1>Подтверждение кода</h1>
<form method="POST" action="{{ route('master.auth.verify-code') }}">
    @csrf
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ session('email') }}" required>
    </div>
    <div>
        <label for="code">Код</label>
        <input type="text" name="code" id="code" required>
    </div>
    <button type="submit">Подтвердить</
