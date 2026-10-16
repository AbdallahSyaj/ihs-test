<?php

namespace App\Http\Requests\Backend\Slider;

use Illuminate\Foundation\Http\FormRequest;

class StoreSliderFormRequest extends FormRequest
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
        $rules = [
            'status' => 'required|numeric|integer|in:1,2',
            'type' => 'required|numeric|integer|in:1,2',
            'title_ar' => 'required|string|max:190',
            'title_en' => 'required|string|max:190',
            'description_ar' => 'nullable',
            'description_en' => 'nullable',
        ];

        if ($this->input('type') == 1) {
            $rules['video'] = 'nullable';
            $rules['image'] = 'required|mimes:png,jpg,jpeg,gif|max:5209';
        }

        if ($this->input('type') == 2) {
            $rules['image'] = 'nullable';
            $rules['video'] = 'required|mimes:mp4,webm,ogv,mov,avi,wmv|max:5209';
        }

        return $rules;
    }
}
