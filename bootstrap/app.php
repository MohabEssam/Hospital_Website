<?php

use App\Http\Middleware\DoctorMiddleware;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Clear any stale config cache that may contain old local values (e.g. 127.0.0.1)
// so Railway environment variables are always read fresh on boot.
$configCachePath = dirname(__DIR__).'/bootstrap/cache/config.php';
if (file_exists($configCachePath)) {
    @unlink($configCachePath);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn (Request $request) => route('login'));
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            if ($user && $user->role === 'doctor') {
                return route('doctor.dashboard');
            }

            if ($user && $user->role === 'patient') {
                return route('patient.dashboard');
            }

            if ($user && $user->role === 'lab') {
                return route('lab.dashboard');
            }

            if ($user && $user->role === 'scan_center') {
                return route('scan-center.dashboard');
            }

            if ($user && $user->role === 'pharmacy') {
                return route('pharmacy.dashboard');
            }

            return route('dashboard');
        });
        $middleware->alias([
            'doctor' => DoctorMiddleware::class,
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
