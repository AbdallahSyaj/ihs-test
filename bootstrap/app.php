<?php

use App\Http\Middleware\EnsurePendingAdminTwoFactor;
use App\Models\SupportTicket;
use App\Support\SupportTicketMessage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\ImportJsonCatalogCommand::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'localize' => LaravelLocalizationRoutes::class,
            'localizationRedirect' => LaravelLocalizationRedirectFilter::class,
            'admin.2fa.pending' => EnsurePendingAdminTwoFactor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Backend controllers used to duplicate this find-or-create-a-SupportTicket
        // logic in a try/catch inside every single action; centralized here instead.
        $exceptions->renderable(function (Throwable $th, Request $request) {
            if (! $request->routeIs('super_admin.*')) {
                return null;
            }

            if ($th instanceof ValidationException
                || $th instanceof AuthenticationException
                || $th instanceof AuthorizationException
                || $th instanceof HttpExceptionInterface) {
                return null;
            }

            $function_name = $request->route()?->getActionName();
            $errorDescription = SupportTicketMessage::forStorage($th->getMessage());

            $check_old_errors = SupportTicket::where([
                'error_location' => $th->getFile(),
                'error_description' => $errorDescription,
                'function_name' => $function_name,
                'error_line' => $th->getLine(),
            ])->get();

            if ($check_old_errors->count() == 0) {
                $end_error_ticket = SupportTicket::create([
                    'error_location' => $th->getFile(),
                    'error_description' => $errorDescription,
                    'function_name' => $function_name,
                    'error_line' => $th->getLine(),
                ]);
            } else {
                $end_error_ticket = $check_old_errors->first();
            }

            return response()->view('errors.support_tickets', compact('th', 'function_name', 'end_error_ticket'));
        });
    })->create();
