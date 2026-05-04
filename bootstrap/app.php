<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Menangani error 419 Page Expired (CSRF Token Mismatch) agar tidak menampilkan layar putih
        $exceptions->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->back()->withInput($request->except('password', '_token'))->withErrors([
                'email' => 'Sesi halaman Anda telah kedaluwarsa karena terlalu lama diam. Silakan coba login kembali.'
            ]);
        });
    })->create();
