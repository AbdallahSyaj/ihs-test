<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Exceptions\AdminTwoFactorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\ConfirmAdminPasswordFormRequest;
use App\Http\Requests\Backend\Auth\ResetAdminTwoFactorFormRequest;
use App\Models\Admin;
use App\Services\AdminTwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorSettingsController extends Controller
{
    public function __construct(private readonly AdminTwoFactorService $twoFactor) {}

    public function show(Request $request): View|RedirectResponse
    {
        $admin = $this->currentAdmin();

        if (! $admin instanceof Admin) {
            return redirect()->route('login');
        }

        return view('admin.two_factor.settings', [
            'admin' => $admin,
            'confirmed' => $admin->hasConfirmedTwoFactor(),
        ]);
    }

    public function recovery(Request $request): View|RedirectResponse
    {
        $codes = $request->session()->pull(AdminTwoFactorService::SESSION_RECOVERY_PLAINTEXT);

        if (! is_array($codes) || $codes === []) {
            return redirect()->route('super_admin.twoFactor-settings')->with('danger', __('dashboard.two_factor.recovery_codes_unavailable'));
        }

        return view('admin.auth.two_factor_recovery', [
            'codes' => $codes,
        ]);
    }

    public function regenerate(ConfirmAdminPasswordFormRequest $request): RedirectResponse
    {
        $admin = $this->currentAdmin();

        if (! $admin instanceof Admin) {
            return redirect()->route('login');
        }

        $passwordCheck = $this->passwordConfirmed($request, $admin, 'recovery_codes_regenerated');

        if ($passwordCheck !== 'ok') {
            return back()->withErrors([
                'current_password' => __($passwordCheck === 'locked'
                    ? 'dashboard.auth.too_many_attempts'
                    : 'dashboard.two_factor.password_incorrect'),
            ]);
        }

        try {
            $codes = $this->twoFactor->regenerateRecoveryCodes($admin);
        } catch (AdminTwoFactorException) {
            return back()->with('danger', __('dashboard.auth.two_factor_unavailable'));
        }

        $request->session()->put(AdminTwoFactorService::SESSION_RECOVERY_PLAINTEXT, $codes);
        $this->twoFactor->audit((int) $admin->getAuthIdentifier(), (int) $admin->getAuthIdentifier(), 'recovery_codes_regenerated', $request->ip());

        return redirect()->route('super_admin.twoFactor-recovery');
    }

    public function disable(ConfirmAdminPasswordFormRequest $request): RedirectResponse
    {
        $admin = $this->currentAdmin();

        if (! $admin instanceof Admin) {
            return redirect()->route('login');
        }

        $passwordCheck = $this->passwordConfirmed($request, $admin, 'two_factor_disabled');

        if ($passwordCheck !== 'ok') {
            return back()->withErrors([
                'current_password' => __($passwordCheck === 'locked'
                    ? 'dashboard.auth.too_many_attempts'
                    : 'dashboard.two_factor.password_incorrect'),
            ]);
        }

        $this->twoFactor->clearTwoFactor($admin);
        $this->twoFactor->audit((int) $admin->getAuthIdentifier(), (int) $admin->getAuthIdentifier(), 'two_factor_disabled', $request->ip());

        return redirect()->route('super_admin.twoFactor-settings')->with('success', __('dashboard.two_factor.disabled'));
    }

    public function reset(ResetAdminTwoFactorFormRequest $request, int $id): RedirectResponse
    {
        $actor = $this->currentAdmin();

        if (! $actor instanceof Admin) {
            return redirect()->route('login');
        }

        $target = Admin::query()->find($id);

        if (! $target instanceof Admin) {
            return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.record_not_found'));
        }

        if ((int) $actor->getAuthIdentifier() === (int) $target->getAuthIdentifier()) {
            return redirect()->route('super_admin.twoFactor-settings')->with('danger', __('dashboard.two_factor.use_self_service'));
        }

        $subject = (string) $actor->getAuthIdentifier();

        if ($this->twoFactor->throttle()->tooMany('sensitive', $subject, $request->ip())) {
            return back()->with('danger', __('dashboard.auth.too_many_attempts'));
        }

        if (strcasecmp((string) $request->input('confirm_email'), (string) $target->email) !== 0) {
            $this->twoFactor->throttle()->hit('sensitive', $subject, $request->ip());

            return back()->with('danger', __('dashboard.two_factor.reset_confirmation_mismatch'));
        }

        $this->twoFactor->clearTwoFactor($target);
        $this->twoFactor->audit((int) $actor->getAuthIdentifier(), (int) $target->getAuthIdentifier(), 'admin_two_factor_reset', $request->ip());

        return redirect()->route('super_admin.admins-show', $target->id)->with('success', __('dashboard.two_factor.reset_successful'));
    }

    private function passwordConfirmed(ConfirmAdminPasswordFormRequest $request, Admin $admin, string $action): string
    {
        $subject = (string) $admin->getAuthIdentifier();

        if ($this->twoFactor->throttle()->tooMany('sensitive', $subject, $request->ip())) {
            return 'locked';
        }

        if (! $this->twoFactor->passwordMatches($admin, (string) $request->input('current_password'))) {
            $this->twoFactor->throttle()->hit('sensitive', $subject, $request->ip());
            $this->twoFactor->audit((int) $admin->getAuthIdentifier(), (int) $admin->getAuthIdentifier(), $action.'_denied', $request->ip());

            return 'invalid';
        }

        return 'ok';
    }

    private function currentAdmin(): ?Admin
    {
        $admin = Auth::guard('super_admin')->user();

        return $admin instanceof Admin ? $admin : null;
    }
}
