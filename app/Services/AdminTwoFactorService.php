<?php

namespace App\Services;

use App\Exceptions\AdminTwoFactorException;
use App\Models\Admin;
use App\Models\AdminSecurityAudit;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;

class AdminTwoFactorService
{
    public const PURPOSE_CHALLENGE = 'challenge';

    public const PURPOSE_ENROLLMENT = 'enrollment';

    public const SESSION_ADMIN_ID = 'super_admin.2fa.admin_id';

    public const SESSION_PURPOSE = 'super_admin.2fa.purpose';

    public const SESSION_EXPIRES_AT = 'super_admin.2fa.expires_at';

    public const SESSION_RECOVERY_PLAINTEXT = 'super_admin.2fa.recovery_plaintext';

    private const RECOVERY_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function __construct(private readonly AdminTwoFactorThrottle $throttle) {}

    public function enforceEnrollment(): bool
    {
        return (bool) config('two_factor.enforce_enrollment');
    }

    public function clearAuthenticationState(Request $request): void
    {
        $request->session()->forget([
            self::SESSION_ADMIN_ID,
            self::SESSION_PURPOSE,
            self::SESSION_EXPIRES_AT,
            self::SESSION_RECOVERY_PLAINTEXT,
        ]);
    }

    public function putPending(Request $request, Admin $admin, string $purpose): void
    {
        $minutes = $purpose === self::PURPOSE_ENROLLMENT
            ? (int) config('two_factor.enrollment_lifetime')
            : (int) config('two_factor.challenge_lifetime');

        $request->session()->put(self::SESSION_ADMIN_ID, (int) $admin->getAuthIdentifier());
        $request->session()->put(self::SESSION_PURPOSE, $purpose);
        $request->session()->put(self::SESSION_EXPIRES_AT, now()->addMinutes($minutes)->getTimestamp());
    }

    public function resolvePendingAdmin(Request $request, string $purpose): ?Admin
    {
        $adminId = $request->session()->get(self::SESSION_ADMIN_ID);
        $storedPurpose = $request->session()->get(self::SESSION_PURPOSE);
        $expiresAt = $request->session()->get(self::SESSION_EXPIRES_AT);

        if (! $this->validPendingShape($adminId, $storedPurpose, $expiresAt, $purpose)) {
            $this->clearAuthenticationState($request);

            return null;
        }

        $admin = Admin::query()->find((int) $adminId);

        if (! $admin instanceof Admin) {
            $this->clearAuthenticationState($request);

            return null;
        }

        if ($purpose === self::PURPOSE_CHALLENGE && ! $admin->hasConfirmedTwoFactor()) {
            $this->clearAuthenticationState($request);

            return null;
        }

        if ($purpose === self::PURPOSE_ENROLLMENT && $admin->hasConfirmedTwoFactor()) {
            $this->clearAuthenticationState($request);

            return null;
        }

        return $admin;
    }

    public function prepareEnrollment(Admin $admin, ?string $ip = null): void
    {
        if ($admin->hasConfirmedTwoFactor()) {
            throw AdminTwoFactorException::unavailable();
        }

        $admin->two_factor_secret = $this->engine()->generateSecretKey((int) config('two_factor.totp.secret_length'));
        $admin->two_factor_confirmed_at = null;
        $admin->two_factor_recovery_codes = null;
        $admin->two_factor_last_used_at = null;
        $admin->save();

        $this->audit(null, (int) $admin->getAuthIdentifier(), 'enrollment_started', $ip);
    }

    public function assertConfirmedSecretReadable(Admin $admin): void
    {
        if (! $admin->hasConfirmedTwoFactor()) {
            return;
        }

        $this->plaintextSecret($admin);
    }

    public function qrCodeSvg(Admin $admin): string
    {
        $uri = $this->provisioningUri($admin);

        try {
            $writer = new Writer(
                new ImageRenderer(
                    new RendererStyle(220),
                    new SvgImageBackEnd
                )
            );
            $svg = $writer->writeString($uri);
        } catch (\Throwable) {
            throw AdminTwoFactorException::unavailable();
        }

        return $this->trustedSvg($svg);
    }

    public function manualKey(Admin $admin): string
    {
        return trim(chunk_split($this->plaintextSecret($admin), 4, ' '));
    }

    /**
     * @return list<string>|null Plaintext recovery codes after a confirmed enrollment.
     */
    public function confirmEnrollment(Admin $admin, string $code): ?array
    {
        if (! $this->codeIsWellFormed($code)) {
            return null;
        }

        $plaintextCodes = null;

        $confirmed = DB::transaction(function () use ($admin, $code, &$plaintextCodes) {
            $locked = Admin::query()->whereKey($admin->getAuthIdentifier())->lockForUpdate()->first();

            if (! $locked instanceof Admin || $locked->hasConfirmedTwoFactor()) {
                return false;
            }

            $timestamp = $this->matchingTimestep($this->plaintextSecret($locked), $code, 0);

            if ($timestamp === null) {
                return false;
            }

            $plaintextCodes = $this->generatePlainRecoveryCodes();
            $locked->two_factor_confirmed_at = now();
            $locked->two_factor_recovery_codes = $this->hashRecoveryCodes($plaintextCodes);
            $locked->two_factor_last_used_at = $timestamp;
            $locked->save();

            return true;
        });

        return $confirmed ? $plaintextCodes : null;
    }

    public function consumeTotp(Admin $admin, string $code): bool
    {
        if (! $this->codeIsWellFormed($code)) {
            return false;
        }

        return DB::transaction(function () use ($admin, $code) {
            $locked = Admin::query()->whereKey($admin->getAuthIdentifier())->lockForUpdate()->first();

            if (! $locked instanceof Admin || ! $locked->hasConfirmedTwoFactor()) {
                return false;
            }

            $previous = (int) ($locked->two_factor_last_used_at ?? 0);
            $timestamp = $this->matchingTimestep($this->plaintextSecret($locked), $code, $previous);

            if ($timestamp === null || $timestamp <= $previous) {
                return false;
            }

            $locked->two_factor_last_used_at = $timestamp;
            $locked->save();

            return true;
        });
    }

    /**
     * @return array{consumed: bool, remaining: int}
     */
    public function consumeRecoveryCode(Admin $admin, string $code): array
    {
        $normalized = $this->normalizeRecoveryCode($code);

        if ($normalized === null) {
            return ['consumed' => false, 'remaining' => 0];
        }

        $hash = $this->hashRecoveryCode($normalized);

        return DB::transaction(function () use ($admin, $hash) {
            $locked = Admin::query()->whereKey($admin->getAuthIdentifier())->lockForUpdate()->first();

            if (! $locked instanceof Admin || ! $locked->hasConfirmedTwoFactor()) {
                return ['consumed' => false, 'remaining' => 0];
            }

            $hashes = array_values($locked->two_factor_recovery_codes ?? []);
            $index = null;

            foreach ($hashes as $position => $stored) {
                if (is_string($stored) && hash_equals($stored, $hash)) {
                    $index = $position;
                    break;
                }
            }

            if ($index === null) {
                return ['consumed' => false, 'remaining' => count($hashes)];
            }

            unset($hashes[$index]);
            $hashes = array_values($hashes);
            $locked->two_factor_recovery_codes = $hashes;
            $locked->save();

            return ['consumed' => true, 'remaining' => count($hashes)];
        });
    }

    /**
     * @return list<string>
     */
    public function regenerateRecoveryCodes(Admin $admin): array
    {
        return DB::transaction(function () use ($admin) {
            $locked = Admin::query()->whereKey($admin->getAuthIdentifier())->lockForUpdate()->first();

            if (! $locked instanceof Admin || ! $locked->hasConfirmedTwoFactor()) {
                throw AdminTwoFactorException::unavailable();
            }

            $plaintextCodes = $this->generatePlainRecoveryCodes();
            $locked->two_factor_recovery_codes = $this->hashRecoveryCodes($plaintextCodes);
            $locked->save();

            return $plaintextCodes;
        });
    }

    public function clearTwoFactor(Admin $admin): void
    {
        DB::transaction(function () use ($admin) {
            $locked = Admin::query()->whereKey($admin->getAuthIdentifier())->lockForUpdate()->first();

            if (! $locked instanceof Admin) {
                return;
            }

            $locked->two_factor_secret = null;
            $locked->two_factor_confirmed_at = null;
            $locked->two_factor_recovery_codes = null;
            $locked->two_factor_last_used_at = null;
            $locked->save();
        });
    }

    public function completeLogin(Request $request, Admin $admin): void
    {
        $fresh = Admin::query()->find($admin->getAuthIdentifier());

        if (! $fresh instanceof Admin) {
            $this->clearAuthenticationState($request);

            throw AdminTwoFactorException::unavailable();
        }

        $this->clearAuthenticationState($request);
        $request->session()->regenerate();
        Auth::guard('super_admin')->login($fresh, false);
    }

    public function passwordMatches(Admin $admin, string $password): bool
    {
        return $password !== '' && Hash::check($password, $admin->getAuthPassword());
    }

    public function audit(?int $actorAdminId, ?int $subjectAdminId, string $action, ?string $ip): void
    {
        AdminSecurityAudit::query()->create([
            'actor_admin_id' => $actorAdminId,
            'subject_admin_id' => $subjectAdminId,
            'action' => $action,
            'ip_address' => $ip,
        ]);

        try {
            Log::channel('security')->info('admin_security_audit', [
                'action' => $action,
                'actor_admin_id' => $actorAdminId,
                'subject_admin_id' => $subjectAdminId,
                'ip' => $ip,
            ]);
        } catch (\Throwable) {
            // The audit row is the record of the event. Logging must not block authentication.
        }
    }

    public function throttle(): AdminTwoFactorThrottle
    {
        return $this->throttle;
    }

    private function provisioningUri(Admin $admin): string
    {
        $issuer = (string) config('app.name', 'Admin Dashboard');

        return $this->engine()->getQRCodeUrl($issuer, (string) $admin->email, $this->plaintextSecret($admin));
    }

    private function plaintextSecret(Admin $admin): string
    {
        try {
            $secret = $admin->two_factor_secret;
        } catch (DecryptException) {
            throw AdminTwoFactorException::unavailable();
        }

        if (! is_string($secret) || $secret === '') {
            throw AdminTwoFactorException::unavailable();
        }

        return $secret;
    }

    private function matchingTimestep(string $secret, string $code, int $previousTimestep): ?int
    {
        try {
            $timestamp = $this->engine()->verifyKeyNewer(
                $secret,
                $code,
                $previousTimestep,
                (int) config('two_factor.totp.window'),
                $this->currentTimestep()
            );
        } catch (\Throwable) {
            throw AdminTwoFactorException::unavailable();
        }

        if (! is_int($timestamp)) {
            return null;
        }

        return $timestamp;
    }

    private function codeIsWellFormed(string $code): bool
    {
        $digits = (int) config('two_factor.totp.digits');

        return (bool) preg_match('/^\d{'.$digits.'}$/', $code);
    }

    /**
     * @return list<string>
     */
    private function generatePlainRecoveryCodes(): array
    {
        $count = (int) config('two_factor.recovery_code_count');
        $codes = [];

        for ($index = 0; $index < $count; $index++) {
            $codes[] = $this->randomRecoveryCode();
        }

        return $codes;
    }

    private function randomRecoveryCode(): string
    {
        $alphabetLength = strlen(self::RECOVERY_ALPHABET);
        $raw = '';

        for ($index = 0; $index < 16; $index++) {
            $raw .= self::RECOVERY_ALPHABET[random_int(0, $alphabetLength - 1)];
        }

        return implode('-', str_split($raw, 4));
    }

    private function normalizeRecoveryCode(string $code): ?string
    {
        $normalized = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $code));

        if (strlen($normalized) !== 16 || ! preg_match('/^[A-Z2-9]+$/', $normalized)) {
            return null;
        }

        return $normalized;
    }

    /**
     * @param  list<string>  $codes
     * @return list<string>
     */
    private function hashRecoveryCodes(array $codes): array
    {
        return array_map(fn (string $code) => $this->hashRecoveryCode($this->normalizeRecoveryCode($code) ?? ''), $codes);
    }

    private function hashRecoveryCode(string $normalized): string
    {
        return hash('sha256', $normalized);
    }

    private function trustedSvg(string $svg): string
    {
        $svg = trim((string) preg_replace('/<\?xml.*?\?>/s', '', $svg));
        $lower = strtolower($svg);

        if (
            ! str_starts_with($svg, '<svg')
            || str_contains($lower, '<script')
            || str_contains($lower, 'otpauth://')
            || str_contains($lower, 'onload=')
            || str_contains($lower, 'javascript:')
        ) {
            throw AdminTwoFactorException::unavailable();
        }

        return $svg;
    }

    private function currentTimestep(): int
    {
        $period = max(1, (int) config('two_factor.totp.period'));

        return (int) floor(now()->getTimestamp() / $period);
    }

    private function engine(): Google2FA
    {
        $google2fa = new Google2FA;
        $google2fa->setWindow((int) config('two_factor.totp.window'));
        $google2fa->setKeyRegeneration((int) config('two_factor.totp.period'));
        $google2fa->setOneTimePasswordLength((int) config('two_factor.totp.digits'));

        return $google2fa;
    }

    private function validPendingShape(mixed $adminId, mixed $purpose, mixed $expiresAt, string $expectedPurpose): bool
    {
        $numericId = is_int($adminId) || (is_string($adminId) && ctype_digit($adminId));
        $numericExpiry = is_int($expiresAt) || (is_string($expiresAt) && ctype_digit((string) $expiresAt));

        return $numericId
            && $purpose === $expectedPurpose
            && $numericExpiry
            && (int) $expiresAt >= now()->getTimestamp();
    }
}
