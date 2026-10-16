<?php

namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;

class AdminTwoFactorThrottle
{
    /**
     * Block when either the subject key or the client IP has exhausted its limit.
     */
    public function tooMany(string $bucket, string $subject, ?string $ip): bool
    {
        [$max] = $this->limits($bucket);

        return RateLimiter::tooManyAttempts($this->key($bucket, $subject, $ip), $max)
            || RateLimiter::tooManyAttempts($this->key($bucket, 'ip', $ip), $max);
    }

    public function hit(string $bucket, string $subject, ?string $ip): void
    {
        [, $decay] = $this->limits($bucket);

        RateLimiter::hit($this->key($bucket, $subject, $ip), $decay);
        RateLimiter::hit($this->key($bucket, 'ip', $ip), $decay);
    }

    public function clear(string $bucket, string $subject, ?string $ip): void
    {
        RateLimiter::clear($this->key($bucket, $subject, $ip));
        RateLimiter::clear($this->key($bucket, 'ip', $ip));
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function limits(string $bucket): array
    {
        $configured = config('two_factor.rate_limits.'.$bucket, []);

        return [
            (int) ($configured['max_attempts'] ?? 5),
            (int) ($configured['decay_seconds'] ?? 60),
        ];
    }

    private function key(string $bucket, string $subject, ?string $ip): string
    {
        return 'admin-2fa|'.$bucket.'|'.$subject.'|'.($ip ?? 'unknown');
    }
}
