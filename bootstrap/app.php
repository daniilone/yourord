<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin/*')) {
                return route('admin.auth.login');
            } elseif ($request->is('master/*')) {
                return route('master.auth.login');
            } elseif ($request->is('client/*')) {
                return route('client.auth.login');
            }
            return route('client.login'); // По умолчанию
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
