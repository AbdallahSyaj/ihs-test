<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Exceptions\AdminTwoFactorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\ConfirmTwoFactorEnrollmentFormRequest;
use App\Models\Admin;
use App\Services\AdminTwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorEnrollmentController extends Controller
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

        try {
            $qrCodeSvg = $this->twoFactor->qrCodeSvg($admin);
            $manualKey = $this->twoFactor->manualKey($admin);
        } catch (AdminTwoFactorException) {
            $this->twoFactor->audit(null, (int) $admin->getAuthIdentifier(), 'two_factor_unavailable', $request->ip());

            return redirect()->route('login')->withErrors([
                'email' => __('dashboard.auth.two_factor_unavailable'),
            ]);
        }

        return view('admin.auth.two_factor_enroll', [
            'qrCodeSvg' => $qrCodeSvg,
            'manualKey' => $manualKey,
            'accountEmail' => $admin->email,
        ]);
    }

    public function store(ConfirmTwoFactorEnrollmentFormRequest $request): RedirectResponse
    {
        $admin = $this->pendingAdmin($request);

        if (! $admin instanceof Admin) {
            return redirect()->route('login')->withErrors([
                'email' => __('dashboard.auth.two_factor_expired'),
            ]);
        }

        $subject = (string) $admin->getAuthIdentifier();

        if ($this->twoFactor->throttle()->tooMany('enrollment', $subject, $request->ip())) {
            return back()->withErrors([
                'code' => __('dashboard.auth.too_many_attempts'),
            ]);
        }

        $this->twoFactor->throttle()->hit('enrollment', $subject, $request->ip());

        try {
            $codes = $this->twoFactor->confirmEnrollment($admin, (string) $request->input('code'));
        } catch (AdminTwoFactorException) {
            $this->twoFactor->audit(null, (int) $admin->getAuthIdentifier(), 'two_factor_unavailable', $request->ip());

            return redirect()->route('login')->withErrors([
                'email' => __('dashboard.auth.two_factor_unavailable'),
            ]);
        }

        if ($codes === null) {
            $this->twoFactor->audit(null, (int) $admin->getAuthIdentifier(), 'two_factor_failed', $request->ip());

            return back()->withErrors([
                'code' => __('dashboard.two_factor.invalid_code'),
            ]);
        }

        try {
            $this->twoFactor->completeLogin($request, $admin);
        } catch (AdminTwoFactorException) {
            return redirect()->route('login')->withErrors([
                'email' => __('dashboard.auth.two_factor_unavailable'),
            ]);
        }

        $request->session()->put(AdminTwoFactorService::SESSION_RECOVERY_PLAINTEXT, $codes);
        $this->twoFactor->audit((int) $admin->getAuthIdentifier(), (int) $admin->getAuthIdentifier(), 'enrollment_confirmed', $request->ip());

        return redirect()->route('super_admin.twoFactor-recovery');
    }

    private function pendingAdmin(Request $request): ?Admin
    {
        return $this->twoFactor->resolvePendingAdmin($request, AdminTwoFactorService::PURPOSE_ENROLLMENT);
    }
}
