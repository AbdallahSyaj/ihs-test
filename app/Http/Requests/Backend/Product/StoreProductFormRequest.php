<?php

namespace App\Http\Requests\Backend\Product;

use App\Http\Requests\Backend\Product\Concerns\PreparesProductSlug;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductFormRequest extends FormRequest
{
    use PreparesProductSlug;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareProductSlug(generateSlugWhenEmpty: true);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge($this->productFieldRules(), $this->nestedRules());
    }

    /**
     * @return array<string, mixed>
     */
    private function productFieldRules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'slug' => $this->productSlugRules(),
            'description' => ['nullable', 'string'],
            'long_description' => ['nullable', 'string'],
            'compatibility' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'badge' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in([Product::STATUS_ACTIVE, Product::STATUS_INACTIVE])],
            'listing_image' => ['nullable', 'mimes:png,jpg,jpeg,gif', 'max:5209'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['mimes:png,jpg,jpeg,gif', 'max:5209'],
            'gallery_orders' => ['nullable', 'array'],
            'gallery_orders.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function nestedRules(): array
    {
        return [
            'specs' => ['nullable', 'array'],
            'specs.*.label' => ['nullable', 'string', 'max:255'],
            'specs.*.value' => ['nullable', 'string', 'max:255'],
            'specs.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'accessories' => ['nullable', 'array'],
            'accessories.*.name' => ['nullable', 'string', 'max:255'],
            'accessories.*.qty' => ['nullable', 'string', 'max:255'],
            'accessories.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'options' => ['nullable', 'array'],
            'options.*.name' => ['nullable', 'string', 'max:255'],
            'options.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'options.*.values' => ['nullable', 'array'],
            'options.*.values.*.label' => ['nullable', 'string', 'max:255'],
            'options.*.values.*.additional_price' => ['nullable', 'numeric', 'min:0'],
            'options.*.values.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
