<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('recurring:generate')->dailyAt('00:05');
        $schedule->command('notifications:generate')->dailyAt('07:00');
        $schedule->command('sitemap:generate')->dailyAt('03:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware aliases
        $middleware->alias([
            'role'   => \App\Http\Middleware\CheckRole::class,
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        // Enforce account-active check on all authenticated routes
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureUserIsActive::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
