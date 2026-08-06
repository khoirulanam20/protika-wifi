<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            $message = 'Sesi berakhir. Silakan coba lagi.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message, 'reload' => true], 419);
            }

            $except = ['_token', 'password', 'password_confirmation', 'current_password'];

            if ($request->user()) {
                return redirect()
                    ->back()
                    ->withInput($request->except($except))
                    ->with('warning', $message);
            }

            return redirect()
                ->route('login')
                ->with('warning', $message);
        });
    })->create();
