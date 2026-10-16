<?php

namespace App\Http\Requests\Backend\TermsConditoins;

use Illuminate\Foundation\Http\FormRequest;

class StoreTermsConditoinsFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
            'title_ar' => 'required',
            'title_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            'status' => 'required|integer|numeric|in:1,2',
        ];
    }
}
