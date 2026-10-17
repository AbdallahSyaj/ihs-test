<?php

namespace App\Http\Requests\Backend\Product\Concerns;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

trait PreparesProductSlug
{
    protected function prepareProductSlug(bool $generateSlugWhenEmpty): void
    {
        $slugInput = $this->input('slug');
        $slugProvided = is_string($slugInput) && trim($slugInput) !== '';
        $slug = null;

        if ($slugProvided) {
            $slug = Str::slug(trim($slugInput));
        } elseif ($generateSlugWhenEmpty) {
            $name = $this->input('name');
            if (is_string($name) && trim($name) !== '') {
                $slug = Str::slug(trim($name));
            }
        }

        if ($slugProvided || $slug !== null) {
            $this->merge(['slug' => $slug ?? '']);
        }
    }

    /**
     * @return array<int, string|\Illuminate\Validation\ValidationRule>
     */
    protected function productSlugRules(?int $ignoreId = null): array
    {
        $unique = Rule::unique('products', 'slug')->whereNull('deleted_at');

        if ($ignoreId !== null) {
            $unique->ignore($ignoreId);
        }

        return [
            'required',
            'string',
            'max:191',
            'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            $unique,
        ];
    }
}
