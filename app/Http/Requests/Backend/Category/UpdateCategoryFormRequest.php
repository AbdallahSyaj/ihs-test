<?php

namespace App\Http\Requests\Backend\Category;

use App\Http\Requests\Backend\Category\Concerns\PreparesCategorySlug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryFormRequest extends FormRequest
{
    use PreparesCategorySlug;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->prepareCategoryNameAndSlug(generateSlugWhenEmpty: false);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = (int) $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($categoryId),
            ],
            'slug' => $this->categorySlugRules($categoryId),
        ];
    }
}
