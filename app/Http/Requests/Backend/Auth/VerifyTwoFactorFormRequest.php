<?php

namespace App\Http\Requests\Backend\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyTwoFactorFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $digits = (int) config('two_factor.totp.digits');

        return [
            'code' => ['nullable', 'required_without:recovery_code', 'regex:/^\d{'.$digits.'}$/'],
            'recovery_code' => ['nullable', 'required_without:code', 'string', 'max:64'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required_without' => __('dashboard.two_factor.invalid_code'),
            'code.regex' => __('dashboard.two_factor.invalid_code'),
            'recovery_code.required_without' => __('dashboard.two_factor.invalid_code'),
        ];
    }
}
