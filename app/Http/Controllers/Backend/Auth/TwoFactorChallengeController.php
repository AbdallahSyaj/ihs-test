<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Exceptions\AdminTwoFactorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\VerifyTwoFactorFormRequest;
use App\Models\Admin;
use App\Services\AdminTwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function __construct(private readonly AdminTwoFactorService $twoFactor) {}

    public function create(Request $request): View|RedirectResponse
    {
        $admin = $this->pendingAdmin($request);

        if (! $admin instanceof Admin) {
            return redirect()->route('login')->withErrors([
                'email' => __('dashboard.auth.two_factor_expired'),
            ]);
        }

        return view('admin.auth.two_factor_challenge');
    }

    public function store(VerifyTwoFactorFormRequest $request): RedirectResponse
    {
        $admin = $this->pendingAdmin($request);

        if (! $admin instanceof Admin) {
            return redirect()->route('login')->withErrors([
                'email' => __('dashboard.auth.two_factor_expired'),
            ]);
        }

        $recoveryCode = trim((string) $request->input('recovery_code', ''));

        if ($recoveryCode !== '') {
            return $this->verifyRecoveryCode($request, $admin, $recoveryCode);
        }

        return $this->verifyTotp($request, $admin, (string) $request->input('code', ''));
    }

    private function verifyTotp(Request $request, Admin $admin, string $code): RedirectResponse
    {
        if ($this->blocked($request, 'verify', $admin)) {
            return $this->tooManyAttempts('code');
        }

        $this->twoFactor->throttle()->hit('verify', (string) $admin->getAuthIdentifier(), $request->ip());

        try {
            $accepted = $this->twoFactor->consumeTotp($admin, $code);
        } catch (AdminTwoFactorException) {
            $this->twoFactor->audit(null, (int) $admin->getAuthIdentifier(), 'two_factor_unavailable', $request->ip());

            return redirect()->route('login')->withErrors([
                'email' => __('dashboard.auth.two_factor_unavailable'),
            ]);
        }

        if (! $accepted) {
            $this->twoFactor->audit(null, (int) $admin->getAuthIdentifier(), 'two_factor_failed', $request->ip());

            return back()->withErrors([
                'code' => __('dashboard.two_factor.invalid_code'),
            ]);
        }

        $this->twoFactor->audit((int) $admin->getAuthIdentifier(), (int) $admin->getAuthIdentifier(), 'two_factor_succeeded', $request->ip());

        return $this->finishLogin($request, $admin);
    }

    private function verifyRecoveryCode(Request $request, Admin $admin, string $recoveryCode): RedirectResponse
    {
        if ($this->blocked($request, 'recovery', $admin)) {
            return $this->tooManyAttempts('recovery_code');
        }

        $this->twoFactor->throttle()->hit('recovery', (string) $admin->getAuthIdentifier(), $request->ip());

        $result = $this->twoFactor->consumeRecoveryCode($admin, $recoveryCode);

        if (! $result['consumed']) {
            $this->twoFactor->audit(null, (int) $admin->getAuthIdentifier(), 'two_factor_failed', $request->ip());

            return back()->withErrors([
                'recovery_code' => __('dashboard.two_factor.invalid_code'),
            ]);
        }

        $this->twoFactor->audit((int) $admin->getAuthIdentifier(), (int) $admin->getAuthIdentifier(), 'recovery_code_used', $request->ip());

        $redirect = $this->finishLogin($request, $admin);

        if ($result['remaining'] === 0) {
            $redirect->with('danger', __('dashboard.two_factor.last_recovery_code_used'));
        }

        return $redirect;
    }

    private function finishLogin(Request $request, Admin $admin): RedirectResponse
    {
        try {
            $this->twoFactor->completeLogin($request, $admin);
        } catch (AdminTwoFactorException) {
            return redirect()->route('login')->withErrors([
                'email' => __('dashboard.auth.two_factor_unavailable'),
            ]);
        }

        return redirect()->intended(route('super_admin.dashboard'));
    }

    private function pendingAdmin(Request $request): ?Admin
    {
        return $this->twoFactor->resolvePendingAdmin($request, AdminTwoFactorService::PURPOSE_CHALLENGE);
    }

    private function blocked(Request $request, string $bucket, Admin $admin): bool
    {
        return $this->twoFactor->throttle()->tooMany($bucket, (string) $admin->getAuthIdentifier(), $request->ip());
    }

    private function tooManyAttempts(string $field): RedirectResponse
    {
        return back()->withErrors([
            $field => __('dashboard.auth.too_many_attempts'),
        ]);
    }
}
