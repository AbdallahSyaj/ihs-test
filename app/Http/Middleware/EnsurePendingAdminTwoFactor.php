<?php

namespace App\Http\Middleware;

use App\Services\AdminTwoFactorService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePendingAdminTwoFactor
{
    public function __construct(private readonly AdminTwoFactorService $twoFactor) {}

    public function handle(Request $request, Closure $next, string $purpose): Response
    {
        if (! in_array($purpose, [AdminTwoFactorService::PURPOSE_CHALLENGE, AdminTwoFactorService::PURPOSE_ENROLLMENT], true)) {
            abort(404);
        }

        $admin = $this->twoFactor->resolvePendingAdmin($request, $purpose);

        if ($admin === null) {
            return redirect()->route('login')->withErrors([
                'email' => __('dashboard.auth.two_factor_expired'),
            ]);
        }

        $request->attributes->set('pendingTwoFactorAdmin', $admin);

        return $next($request);
    }
}
