<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Lang;

class MoneyTransferRequest extends FormRequest
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
            'from' => 'required|fromCard',
            'to' => 'required|toCard|size:16',
            'price' => 'required|integer|between:1000,50000000',
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
            'from.from_card' =>  __('messages.from.fromCard' ),
            'price.digits_between' => __('messages.price.digits_between' , ['from' => 1000 , 'to' => 50000000]),
            'price.validation.between.numeric' => __('messages.price.digits_between' , ['from' => 1000 , 'to' => 50000000]),
            'price.between.numeric' => __('messages.price.digits_between' , ['from' => 1000 , 'to' => 50000000]),
            'price.numeric' => __('messages.price.digits_between' , ['from' => 1000 , 'to' => 50000000]),
            'price.between' => __('messages.price.digits_between' , ['from' => 1000 , 'to' => 50000000]),
            'to.to_card' =>__('messages.to.toCard'),
            'to.size' =>__('messages.to.toCard'),
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
            'from' => $this->toEnglish($this->from),
            'to' => $this->toEnglish($this->to),
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
