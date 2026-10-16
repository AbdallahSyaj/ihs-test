<?php

namespace App\Http\Requests\Backend\AboutUs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutUsFormRequest extends FormRequest
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

    public function rules(): array
    {
        return [
            'title_ar' => 'required',
            'title_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            // Laravel 12 excludes SVG from the `image` rule by default;
            // allow_svg restores it since mimes: below already permits svg.
            'image' => 'nullable|image:allow_svg|mimes:webp,jpeg,png,jpg,gif,svg|max:4048',
        ];
    }
}
