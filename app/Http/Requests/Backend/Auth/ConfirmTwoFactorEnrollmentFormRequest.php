<?php

namespace App\Http\Requests\Backend\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmTwoFactorEnrollmentFormRequest extends FormRequest
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
            'code' => ['required', 'regex:/^\d{'.$digits.'}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => __('dashboard.two_factor.invalid_code'),
            'code.regex' => __('dashboard.two_factor.invalid_code'),
        ];
    }
}
