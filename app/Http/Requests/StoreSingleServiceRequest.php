<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSingleServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('Service_translations', 'name')->ignore($this->input('id'), 'Service_id'),
            ],
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم الخدمة مطلوب.',
            'name.unique' => 'اسم الخدمة مستخدم مسبقاً.',
            'price.required' => 'سعر الخدمة مطلوب.',
            'price.numeric' => 'سعر الخدمة يجب أن يكون رقماً.',
        ];
    }
}
