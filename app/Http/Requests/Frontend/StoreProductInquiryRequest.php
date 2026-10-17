<?php

namespace App\Http\Requests\Frontend;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProductInquiryRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.slug' => ['required', 'string', 'max:255'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.image' => ['nullable', 'string', 'max:2048'],
            'items.*.model' => ['nullable', 'string', 'max:255'],
            'items.*.options' => ['nullable', 'array'],
            'items.*.options.*' => ['string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('items', []) as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }

                $slug = $item['slug'] ?? null;
                if (! is_string($slug) || $slug === '') {
                    continue;
                }

                if (Product::query()->where('slug', $slug)->where('status', Product::STATUS_ACTIVE)->doesntExist()) {
                    $validator->errors()->add(
                        "items.$index.slug",
                        'One or more selected products are no longer available.'
                    );
                }
            }
        });
    }
}
