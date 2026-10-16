<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Exceptions\AdminTwoFactorException;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\AdminTwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthBackendController extends Controller
{
    public function __construct(private readonly AdminTwoFactorService $twoFactor) {}

    // =================================================================
    // ===================== showLoginForm =============================
    // ===================== Created By:Qusai Alnablse ==================
    // =================================================================
    public function showLoginForm()
    {
        if (Auth::guard('super_admin')->check()) {
            return redirect()->intended(route('super_admin.dashboard'));
        }

        return view('admin.auth.login');
    }

    // =================================================================
    // ===================== loginFormSubmit ===========================
    // ===================== Created By:Qusai Alnablse ==================
    // =================================================================
    public function loginFormSubmit(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $subject = Str::lower(trim((string) $request->input('email')));

        if ($this->twoFactor->throttle()->tooMany('login', $subject, $request->ip())) {
            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => __('dashboard.auth.too_many_attempts'),
            ]);
        }

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (! Auth::guard('super_admin')->validate($credentials)) {
            $this->twoFactor->throttle()->hit('login', $subject, $request->ip());

            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => __('dashboard.auth.invalid_credentials'),
            ]);
        }

        $attempted = Auth::guard('super_admin')->getLastAttempted();
        $admin = $attempted instanceof Admin
            ? Admin::query()->find($attempted->getAuthIdentifier())
            : null;

        if (! $admin instanceof Admin) {
            $this->twoFactor->throttle()->hit('login', $subject, $request->ip());

            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => __('dashboard.auth.invalid_credentials'),
            ]);
        }

        $this->twoFactor->clearAuthenticationState($request);

        if ($admin->hasConfirmedTwoFactor()) {
            try {
                $this->twoFactor->assertConfirmedSecretReadable($admin);
            } catch (AdminTwoFactorException) {
                $this->twoFactor->audit(null, (int) $admin->getAuthIdentifier(), 'two_factor_unavailable', $request->ip());

                return back()->withInput($request->only('email', 'remember'))->withErrors([
                    'email' => __('dashboard.auth.two_factor_unavailable'),
                ]);
            }

            $this->twoFactor->putPending($request, $admin, AdminTwoFactorService::PURPOSE_CHALLENGE);

            return redirect()->route('super_admin.twoFactor-challenge');
        }

        if ($this->twoFactor->enforceEnrollment()) {
            try {
                $this->twoFactor->prepareEnrollment($admin, $request->ip());
            } catch (AdminTwoFactorException) {
                return back()->withInput($request->only('email', 'remember'))->withErrors([
                    'email' => __('dashboard.auth.two_factor_unavailable'),
                ]);
            }

            $this->twoFactor->putPending($request, $admin, AdminTwoFactorService::PURPOSE_ENROLLMENT);

            return redirect()->route('super_admin.twoFactor-enroll');
        }

        Auth::guard('super_admin')->login($admin, false);

        return redirect()->intended(route('super_admin.dashboard'));
    }

    // =================================================================
    // ===================== logout ====================================
    // ===================== Created By:Qusai Alnablse ==================
    // =================================================================
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('super_admin')->logout();
        $this->twoFactor->clearAuthenticationState($request);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
