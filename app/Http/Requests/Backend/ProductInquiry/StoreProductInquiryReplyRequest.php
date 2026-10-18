<?php

namespace App\Http\Requests\Backend\ProductInquiry;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductInquiryReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('super_admin')->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
        ];
    }
}
