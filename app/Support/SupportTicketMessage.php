<?php

namespace App\Support;

class SupportTicketMessage
{
    /**
     * Remove authentication material before a super-admin exception is stored.
     */
    public static function forStorage(string $message): string
    {
        $needles = [
            'otpauth://',
            'two_factor_secret',
            'two_factor_recovery',
            'secret=',
        ];

        foreach ($needles as $needle) {
            if (stripos($message, $needle) !== false) {
                return 'A two-factor authentication error occurred.';
            }
        }

        return $message;
    }
}
