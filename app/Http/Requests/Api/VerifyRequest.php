<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Lang;

class VerifyRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'mobile' => 'required|phone_number|size:11',
             'verify_code' => 'required',
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'mobile.required' =>  __('messages.mobile.required'),
            'verify_code.required' => __('messages.verify_code.required'),
            'mobile.phone_number' =>  __('messages.validation.phone_number'),
            'mobile.size' =>  __('messages.validation.phone_number.size'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => $validator->errors()
        ]));
    }


    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'mobile' => $this->toEnglish($this->mobile),
            'verify_code' => $this->toEnglish($this->verify_code),
        ]);
    }

    public function toEnglish($string): array|string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١','٠'];
        $num = range(0, 9);
        $convertedPersianNums = str_replace($persian, $num, $string);
        return str_replace($arabic, $num, $convertedPersianNums);
    }
}
