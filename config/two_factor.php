<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mandatory enrollment
    |--------------------------------------------------------------------------
    |
    | When true, an administrator who has not confirmed an authenticator must
    | enroll before the dashboard session is created. Confirmed administrators
    | are always challenged, even when this flag is later turned off.
    |
    */

    'enforce_enrollment' => (bool) env('TWO_FACTOR_ENFORCE_ENROLLMENT', true),

    /*
    |--------------------------------------------------------------------------
    | TOTP
    |--------------------------------------------------------------------------
    |
    | Six digits and a 30 second period match Google Authenticator and
    | Microsoft Authenticator. The window is one step either side of the
    | current timestep (about ±30 seconds).
    |
    */

    'totp' => [
        'digits' => 6,
        'period' => 30,
        'window' => 1,
        'secret_length' => 32,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pending session lifetime (minutes)
    |--------------------------------------------------------------------------
    */

    'challenge_lifetime' => 5,

    'enrollment_lifetime' => 10,

    /*
    |--------------------------------------------------------------------------
    | Recovery codes
    |--------------------------------------------------------------------------
    */

    'recovery_code_count' => 8,

    /*
    |--------------------------------------------------------------------------
    | Rate limits
    |--------------------------------------------------------------------------
    |
    | max_attempts applies independently to the account/challenge key and to
    | the client IP. decay_seconds is the lockout window.
    |
    */

    'rate_limits' => [
        'login' => ['max_attempts' => 5, 'decay_seconds' => 60],
        'verify' => ['max_attempts' => 5, 'decay_seconds' => 60],
        'recovery' => ['max_attempts' => 5, 'decay_seconds' => 60],
        'enrollment' => ['max_attempts' => 5, 'decay_seconds' => 60],
        'sensitive' => ['max_attempts' => 5, 'decay_seconds' => 60],
    ],

];
