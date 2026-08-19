<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
// ⬇️ import middleware yang kamu punya

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\Auth\CheckRole;
use App\Http\Middleware\VerifyRecaptcha;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ⬇️ PENTING: tempelkan Inertia middleware ke group "web"
        $middleware->appendToGroup('web', [
            HandleInertiaRequests::class,
           \App\Http\Middleware\BindUserToSession::class,

        ]);

        // ⬇️ alias custom middleware
        $middleware->alias([
            'role' => CheckRole::class,
            'verify.recaptcha' => VerifyRecaptcha::class, // kalau dipakai
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (TokenMismatchException $e) {
            $req = request();
            $short = fn($v) => $v ? Str::limit($v, 16, '…') : null;

            Log::warning('CSRF mismatch detected', [
                'path'            => $req?->path(),
                'method'          => $req?->method(),
                'origin'          => $req?->headers->get('Origin'),
                'referer'         => $req?->headers->get('Referer'),
                'host'            => $req?->getHost(),
                'session_id'      => $req?->session()?->getId(),
                'has_cookie_xsrf' => $req?->cookies->has('XSRF-TOKEN'),
                'has_cookie_sess' => $req?->cookies->has(config('session.cookie')),
                'token_session'   => $short($req?->session()?->token()),
                'token_input'     => $short($req?->input('_token')),
                'token_header'    => $short($req?->header('X-CSRF-TOKEN')),
                'token_x_xsrf'    => $short($req?->header('X-XSRF-TOKEN')),
                'session_driver'  => config('session.driver'),
                'app_url'         => config('app.url'),
                'session_domain'  => config('session.domain'),
                'secure_cookie'   => config('session.secure'),
                'samesite'        => config('session.same_site'),
            ]);

                \Log::debug('EXCEPTION-CATCHALL', [
        'type' => get_class($e),
        'msg'  => $e->getMessage(),
    ]);
        });

        // (opsional) render JSON untuk SPA:
        // $exceptions->render(function (TokenMismatchException $e, $request) {
        //     if ($request->expectsJson()) {
        //         return response()->json(['message' => 'CSRF token mismatch'], 419);
        //     }
        // });
    })
    ->create();