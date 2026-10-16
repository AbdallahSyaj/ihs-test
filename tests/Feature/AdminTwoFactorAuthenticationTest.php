<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AdminSecurityAudit;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\AdminTwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use PragmaRX\Google2FA\Google2FA;
use ReflectionMethod;
use RuntimeException;
use Tests\TestCase;

class AdminTwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_still_renders(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_invalid_password_does_not_authenticate_an_admin(): void
    {
        $admin = Admin::factory()->create();

        $this->post(route('super_admin.loginFormSubmit'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('super_admin');
    }

    public function test_unenrolled_admin_is_sent_to_enrollment_and_cannot_open_the_dashboard(): void
    {
        $admin = Admin::factory()->create();

        $this->postLogin($admin)->assertRedirect(route('super_admin.twoFactor-enroll'));
        $this->assertGuest('super_admin');

        $this->get(route('super_admin.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest('super_admin');
    }

    public function test_confirmed_admin_is_challenged_and_not_authenticated_before_the_code(): void
    {
        $admin = $this->enrolledAdmin();

        $this->get(route('super_admin.dashboard'));
        $this->postLogin($admin)->assertRedirect(route('super_admin.twoFactor-challenge'));
        $this->assertGuest('super_admin');

        $this->get(route('super_admin.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest('super_admin');
    }

    public function test_valid_totp_completes_authentication_and_keeps_the_intended_url(): void
    {
        $admin = $this->enrolledAdmin();
        $this->travel(35)->seconds();

        $this->get(route('super_admin.users-index'));
        $this->postLogin($admin);

        $this->post(route('super_admin.twoFactor-verify'), [
            'code' => $this->currentCode($admin),
        ])->assertRedirect(route('super_admin.users-index'));

        $this->assertAuthenticatedAs($admin, 'super_admin');
    }

    public function test_wrong_malformed_expired_and_replayed_codes_fail(): void
    {
        $admin = $this->enrolledAdmin();
        $this->travel(35)->seconds();
        $secret = $admin->two_factor_secret;

        $this->postLogin($admin);
        $this->post(route('super_admin.twoFactor-verify'), [
            'code' => $this->wrongCode($secret),
        ])->assertSessionHasErrors('code');
        $this->assertGuest('super_admin');

        $this->post(route('super_admin.twoFactor-verify'), [
            'code' => '12',
        ])->assertSessionHasErrors('code');
        $this->assertDatabaseCount('support_tickets', 0);

        $this->travel(6)->minutes();
        $this->post(route('super_admin.twoFactor-verify'), [
            'code' => $this->currentCode($admin),
        ])->assertRedirect(route('login'));
        $this->assertGuest('super_admin');

        $code = $this->currentCode($admin);
        $this->postLogin($admin);
        $this->post(route('super_admin.twoFactor-verify'), ['code' => $code])
            ->assertRedirect(route('super_admin.dashboard'));

        $this->post(route('super_admin.logout'));
        $this->postLogin($admin);
        $this->post(route('super_admin.twoFactor-verify'), ['code' => $code])
            ->assertSessionHasErrors('code');
        $this->assertGuest('super_admin');

        $failed = AdminSecurityAudit::query()->where('action', 'two_factor_failed')->first();
        $this->assertNotNull($failed);
        $this->assertStringNotContainsString($code, json_encode($failed->getAttributes()));
    }

    public function test_recovery_code_authenticates_once_and_then_fails(): void
    {
        $admin = Admin::factory()->create();
        $codes = $this->recoveryCodesFor($admin);

        $this->postLogin($admin);
        $this->post(route('super_admin.twoFactor-verify'), [
            'recovery_code' => $codes[0],
        ])->assertRedirect(route('super_admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'super_admin');

        $this->post(route('super_admin.logout'));
        $this->postLogin($admin);
        $this->post(route('super_admin.twoFactor-verify'), [
            'recovery_code' => $codes[0],
        ])->assertSessionHasErrors('recovery_code');
        $this->assertGuest('super_admin');
    }

    public function test_regular_users_and_the_users_table_are_unchanged(): void
    {
        $this->assertFalse(Schema::hasColumn('users', 'two_factor_secret'));

        User::query()->create([
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => 'regular@example.com',
            'phone' => '5551234567',
            'password' => 'password',
            'status' => 1,
        ]);

        $this->post(route('super_admin.loginFormSubmit'), [
            'email' => 'regular@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('super_admin');
        $this->get(route('super_admin.users-index'))->assertRedirect(route('login'));
    }

    public function test_enrollment_requires_a_valid_code_and_shows_recovery_codes_once(): void
    {
        $admin = Admin::factory()->create();

        $this->postLogin($admin);
        $page = $this->get(route('super_admin.twoFactor-enroll'));
        $page->assertOk();
        $page->assertSee('<svg', false);
        $page->assertDontSee('otpauth://', false);

        $secret = $admin->fresh()->two_factor_secret;
        $storedSecret = DB::table('admins')->where('id', $admin->id)->value('two_factor_secret');
        $this->assertIsString($secret);
        $this->assertStringNotContainsString($secret, (string) $storedSecret);
        $this->assertNull($admin->fresh()->two_factor_confirmed_at);

        $this->post(route('super_admin.twoFactor-enrollConfirm'), [
            'code' => $this->wrongCode($secret),
        ])->assertSessionHasErrors('code');
        $this->assertNull($admin->fresh()->two_factor_confirmed_at);
        $this->assertGuest('super_admin');

        $response = $this->post(route('super_admin.twoFactor-enrollConfirm'), [
            'code' => $this->currentCode($admin),
        ]);
        $response->assertRedirect(route('super_admin.twoFactor-recovery'));
        $this->assertAuthenticatedAs($admin, 'super_admin');

        $codesPage = $this->get(route('super_admin.twoFactor-recovery'));
        $codesPage->assertOk();
        preg_match('/[A-Z2-9]{4}-[A-Z2-9]{4}-[A-Z2-9]{4}-[A-Z2-9]{4}/', $codesPage->getContent(), $matches);
        $this->assertNotEmpty($matches);
        $this->assertStringNotContainsString($matches[0], (string) DB::table('admins')->where('id', $admin->id)->value('two_factor_recovery_codes'));

        $this->get(route('super_admin.twoFactor-recovery'))
            ->assertRedirect(route('super_admin.twoFactor-settings'));
    }

    public function test_regeneration_invalidates_previous_recovery_codes(): void
    {
        $admin = Admin::factory()->create();
        $oldCode = $this->recoveryCodesFor($admin)[0];

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.twoFactor-regenerateRecoveryCodes'), [
                'current_password' => 'password',
            ])->assertRedirect(route('super_admin.twoFactor-recovery'));

        $this->assertFalse(app(AdminTwoFactorService::class)->consumeRecoveryCode($admin->fresh(), $oldCode)['consumed']);

        $page = $this->get(route('super_admin.twoFactor-recovery'));
        preg_match('/[A-Z2-9]{4}-[A-Z2-9]{4}-[A-Z2-9]{4}-[A-Z2-9]{4}/', $page->getContent(), $matches);
        $this->assertTrue(app(AdminTwoFactorService::class)->consumeRecoveryCode($admin->fresh(), $matches[0])['consumed']);
    }

    public function test_disable_requires_the_current_password_and_clears_two_factor_state(): void
    {
        $admin = $this->enrolledAdmin();

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.twoFactor-disable'), [
                'current_password' => 'wrong-password',
            ])->assertSessionHasErrors('current_password');

        $this->assertNotNull($admin->fresh()->two_factor_confirmed_at);

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.twoFactor-disable'), [
                'current_password' => 'password',
            ])->assertRedirect(route('super_admin.twoFactor-settings'));

        $fresh = $admin->fresh();
        $this->assertNull($fresh->two_factor_confirmed_at);
        $this->assertNull($fresh->two_factor_secret);
        $this->assertNull($fresh->two_factor_recovery_codes);
        $this->assertNull($fresh->two_factor_last_used_at);
        $this->assertAuthenticatedAs($admin, 'super_admin');
    }

    public function test_another_admin_can_reset_two_factor_and_the_target_must_enroll_again(): void
    {
        $operator = Admin::factory()->create();
        $target = $this->enrolledAdmin();
        $secret = $target->two_factor_secret;

        $this->post(route('super_admin.admins-resetTwoFactor', $target->id), [
            'confirm_email' => $target->email,
        ])->assertRedirect(route('login'));
        $this->assertNotNull($target->fresh()->two_factor_confirmed_at);

        $user = User::query()->create([
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => 'reset-user@example.com',
            'phone' => '5550002222',
            'password' => 'password',
            'status' => 1,
        ]);
        $this->actingAs($user, 'web')
            ->post(route('super_admin.admins-resetTwoFactor', $target->id), [
                'confirm_email' => $target->email,
            ])->assertRedirect(route('login'));

        $this->actingAs($target, 'super_admin')
            ->post(route('super_admin.admins-resetTwoFactor', $target->id), [
                'confirm_email' => $target->email,
            ])->assertRedirect(route('super_admin.twoFactor-settings'));
        $this->assertNotNull($target->fresh()->two_factor_confirmed_at);

        $this->actingAs($operator, 'super_admin')
            ->post(route('super_admin.admins-resetTwoFactor', $target->id), [
                'confirm_email' => 'someone-else@example.com',
            ])->assertSessionHas('danger');
        $this->assertNotNull($target->fresh()->two_factor_confirmed_at);

        $this->actingAs($operator, 'super_admin')
            ->post(route('super_admin.admins-resetTwoFactor', $target->id), [
                'confirm_email' => $target->email,
            ])->assertRedirect(route('super_admin.admins-show', $target->id));

        $this->assertNull($target->fresh()->two_factor_confirmed_at);
        $audit = AdminSecurityAudit::query()->where('action', 'admin_two_factor_reset')->first();
        $this->assertNotNull($audit);
        $this->assertSame($operator->id, $audit->actor_admin_id);
        $this->assertSame($target->id, $audit->subject_admin_id);
        $this->assertStringNotContainsString($secret, json_encode($audit->getAttributes()));

        $this->post(route('super_admin.logout'));
        $this->postLogin($target)->assertRedirect(route('super_admin.twoFactor-enroll'));
        $this->assertGuest('super_admin');
    }

    public function test_logout_removes_pending_state(): void
    {
        $admin = $this->enrolledAdmin();
        $this->postLogin($admin)->assertRedirect(route('super_admin.twoFactor-challenge'));

        $this->post(route('super_admin.logout'))->assertRedirect('/');
        $this->assertGuest('super_admin');
        $this->get(route('super_admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('super_admin.twoFactor-challenge'))->assertRedirect(route('login'));
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        config(['two_factor.rate_limits.login.max_attempts' => 2]);
        $admin = Admin::factory()->create();

        $this->postLogin($admin, 'wrong-password');
        $this->postLogin($admin, 'wrong-password');
        $this->from(route('login'))
            ->post(route('super_admin.loginFormSubmit'), [
                'email' => $admin->email,
                'password' => 'password',
            ])
            ->assertSessionHasErrors('email');

        $this->assertGuest('super_admin');
    }

    public function test_rate_limits_block_repeated_verification_attempts(): void
    {
        config(['two_factor.rate_limits.verify.max_attempts' => 2]);
        $admin = $this->enrolledAdmin();
        $this->travel(35)->seconds();
        $secret = $admin->two_factor_secret;
        $this->postLogin($admin);

        $this->post(route('super_admin.twoFactor-verify'), ['code' => $this->wrongCode($secret)]);
        $this->post(route('super_admin.twoFactor-verify'), ['code' => $this->wrongCode($secret)]);
        $this->post(route('super_admin.twoFactor-verify'), ['code' => $this->currentCode($admin)])
            ->assertSessionHasErrors('code');
        $this->assertGuest('super_admin');
    }

    public function test_the_same_recovery_code_and_totp_timestep_cannot_be_consumed_twice(): void
    {
        $admin = Admin::factory()->create();
        $codes = $this->recoveryCodesFor($admin);
        $service = app(AdminTwoFactorService::class);
        $before = $admin->fresh()->two_factor_last_used_at;

        $first = $service->consumeRecoveryCode($admin->fresh(), $codes[0]);
        $second = $service->consumeRecoveryCode($admin->fresh(), $codes[0]);

        $this->assertTrue($first['consumed']);
        $this->assertFalse($second['consumed']);
        $this->assertSame($before, $admin->fresh()->two_factor_last_used_at);

        $this->travel(35)->seconds();
        $code = $this->currentCode($admin);
        $this->assertTrue($service->consumeTotp($admin->fresh(), $code));
        $this->assertFalse($service->consumeTotp($admin->fresh(), $code));
    }

    public function test_a_confirmed_secret_that_cannot_be_decrypted_fails_closed(): void
    {
        $admin = $this->enrolledAdmin();

        DB::table('admins')->where('id', $admin->id)->update([
            'two_factor_secret' => 'not-valid-ciphertext',
        ]);

        $this->postLogin($admin)->assertSessionHasErrors('email');
        $this->assertGuest('super_admin');
        $this->assertNotNull($admin->fresh()->two_factor_confirmed_at);
        $this->assertSame(
            'not-valid-ciphertext',
            DB::table('admins')->where('id', $admin->id)->value('two_factor_secret')
        );
        $this->get(route('super_admin.twoFactor-enroll'))->assertRedirect(route('login'));
    }

    public function test_support_ticket_storage_strips_two_factor_secrets(): void
    {
        Route::middleware('web')->get('/testing-two-factor-exception', function () {
            throw new RuntimeException('otpauth://totp/Admin?secret=JBSWY3DPEHPK3PXP');
        })->name('super_admin.testing_two_factor_exception');

        $response = $this->get('/testing-two-factor-exception');
        $response->assertDontSee('JBSWY3DPEHPK3PXP', false);
        $response->assertDontSee('otpauth://', false);

        $ticket = SupportTicket::query()->first();
        $this->assertNotNull($ticket);
        $this->assertSame('A two-factor authentication error occurred.', $ticket->error_description);
        $this->assertStringNotContainsString('JBSWY3DPEHPK3PXP', $ticket->error_description);
    }

    public function test_confirmed_admins_are_challenged_when_enrollment_enforcement_is_off(): void
    {
        config(['two_factor.enforce_enrollment' => false]);
        $admin = $this->enrolledAdmin();

        $this->postLogin($admin)->assertRedirect(route('super_admin.twoFactor-challenge'));
        $this->assertGuest('super_admin');
    }

    public function test_a_submitted_admin_id_cannot_switch_the_pending_challenge(): void
    {
        $admin = $this->enrolledAdmin();
        $other = $this->enrolledAdmin();
        $this->travel(35)->seconds();

        $this->postLogin($admin);
        $this->post(route('super_admin.twoFactor-verify'), [
            'code' => $this->currentCode($other),
            'admin_id' => $other->id,
        ])->assertSessionHasErrors('code');

        $this->assertGuest('super_admin');
    }

    private function postLogin(Admin $admin, string $password = 'password')
    {
        return $this->post(route('super_admin.loginFormSubmit'), [
            'email' => $admin->email,
            'password' => $password,
        ]);
    }

    private function enrolledAdmin(): Admin
    {
        $admin = Admin::factory()->create();
        $this->recoveryCodesFor($admin);

        return $admin->fresh();
    }

    /**
     * @return list<string>
     */
    private function recoveryCodesFor(Admin $admin): array
    {
        $service = app(AdminTwoFactorService::class);
        $service->prepareEnrollment($admin->fresh(), '127.0.0.1');
        $codes = $service->confirmEnrollment($admin->fresh(), $this->currentCode($admin));
        $this->assertIsArray($codes);

        return $codes;
    }

    private function currentCode(Admin $admin): string
    {
        return $this->codeAtTimestep($admin->fresh()->two_factor_secret, $this->timestep());
    }

    private function wrongCode(string $secret): string
    {
        $current = (int) $this->codeAtTimestep($secret, $this->timestep());

        return str_pad((string) (($current + 500000) % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function codeAtTimestep(string $secret, int $timestep): string
    {
        $google2fa = new Google2FA;
        $method = new ReflectionMethod($google2fa, 'oathTotp');
        $method->setAccessible(true);

        return $method->invoke($google2fa, $secret, $timestep);
    }

    private function timestep(): int
    {
        return (int) floor(now()->getTimestamp() / (int) config('two_factor.totp.period'));
    }
}
