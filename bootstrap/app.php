<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Di sinilah kita mendaftarkan middleware alias kita
        $middleware->alias([
            'cekperan' => \App\Http\Middleware\CekPeran::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();