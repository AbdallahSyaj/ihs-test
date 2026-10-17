<?php

namespace App\Http\Requests\Backend\Category\Concerns;

use App\Models\Category;
use Illuminate\Support\Str;

trait PreparesCategorySlug
{
    protected function prepareCategoryNameAndSlug(bool $generateSlugWhenEmpty): void
    {
        $name = $this->input('name');
        if (is_string($name)) {
            $name = trim($name);
        }

        $slugInput = $this->input('slug');
        $slugProvided = is_string($slugInput) && trim($slugInput) !== '';
        $slug = null;

        if ($slugProvided) {
            $slug = Str::slug(trim($slugInput));
        } elseif ($generateSlugWhenEmpty && is_string($name) && $name !== '') {
            $slug = Category::slugFromName($name);
        }

        $merge = [];
        if (is_string($name)) {
            $merge['name'] = $name;
        }
        if ($slugProvided || $slug !== null) {
            $merge['slug'] = $slug ?? '';
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<int, string>
     */
    protected function categorySlugRules(?int $ignoreId = null): array
    {
        $unique = \Illuminate\Validation\Rule::unique('categories', 'slug')
            ->whereNull('deleted_at');

        if ($ignoreId !== null) {
            $unique->ignore($ignoreId);
        }

        return [
            'required',
            'string',
            'max:255',
            'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            $unique,
        ];
    }
}
