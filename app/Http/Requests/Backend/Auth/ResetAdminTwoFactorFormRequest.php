<?php

namespace App\Http\Requests\Backend\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ResetAdminTwoFactorFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('super_admin')->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'confirm_email' => ['required', 'email'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'confirm_email.required' => __('dashboard.two_factor.reset_confirmation_mismatch'),
            'confirm_email.email' => __('dashboard.two_factor.reset_confirmation_mismatch'),
        ];
    }
}
