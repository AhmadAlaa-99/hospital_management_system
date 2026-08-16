<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreInsuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'insurance_code' => 'required',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'Company_rate' => 'required|numeric|min:0|max:100',
            'name' => 'required|unique:insurance_translations,name,'.$this->id,
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $patient = (float) $this->input('discount_percentage', 0);
            $company = (float) $this->input('Company_rate', 0);

            if (abs($patient + $company - 100) > 0.01) {
                $validator->errors()->add(
                    'Company_rate',
                    'مجموع نسبة تحمل المريض وشركة التأمين يجب أن يساوي 100%.'
                );
            }
        });
    }

    public function messages()
    {
        return [
            'insurance_code.required' => trans('validation.required'),
            'discount_percentage.required' => 'نسبة تحمل المريض مطلوبة.',
            'discount_percentage.numeric' => 'نسبة تحمل المريض يجب أن تكون رقماً.',
            'discount_percentage.min' => 'نسبة تحمل المريض يجب ألا تكون سالبة.',
            'discount_percentage.max' => 'نسبة تحمل المريض يجب ألا تتجاوز 100%.',
            'Company_rate.required' => 'نسبة تحمل شركة التأمين مطلوبة.',
            'Company_rate.numeric' => 'نسبة تحمل شركة التأمين يجب أن تكون رقماً.',
            'Company_rate.min' => 'نسبة تحمل شركة التأمين يجب ألا تكون سالبة.',
            'Company_rate.max' => 'نسبة تحمل شركة التأمين يجب ألا تتجاوز 100%.',
            'name.required' => trans('validation.required'),
            'name.unique' => trans('validation.unique'),
        ];
    }
}
