<?php

namespace App\Http\Requests\Backend\ContactUs;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateContactUsFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'phone' => 'required',
            'whatsapp' => 'required',
            'address_ar' => 'required|string',
            'address_en' => 'required|string',
            'hours' => ['nullable', 'string', 'max:5000'],
            'facebook' => $this->socialLinkRule(),
            'twitter' => $this->socialLinkRule(),
            'instagram' => $this->socialLinkRule(),
            'linkedin' => $this->socialLinkRule(),
            'youtube' => $this->socialLinkRule(),
            'snapchat' => $this->socialLinkRule(),
            'tiktok' => $this->socialLinkRule(),
            'telegram' => $this->socialLinkRule(),
        ];
    }

    /**
     * Accept a full http(s) URL or a scheme-less host such as www.facebook.com.
     * The submitted value is stored unchanged.
     *
     * @return array<int, mixed>
     */
    private function socialLinkRule(): array
    {
        return ['nullable', 'string', function (string $attribute, mixed $value, Closure $fail): void {
            if (! is_string($value) || trim($value) === '') {
                return;
            }

            $candidate = preg_match('#^[a-z][a-z0-9+.-]*://#i', $value) === 1
                ? $value
                : 'https://'.$value;

            if (! Str::isUrl($candidate, ['http', 'https'])) {
                $fail(__('validation.url', ['attribute' => str_replace('_', ' ', $attribute)]));
            }
        }];
    }

}

