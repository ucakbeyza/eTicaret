<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'card_number' => ['required','digits_between:13,19'],
            'expiry_date' => ['required','regex:/^\d{2}\/\d{2,4}$/'],
            'cvv' => ['required','digits_between:3,4'],
            'shipping_company_id' => 'required|exists:shipping_companies,id',
            'address' => 'required|string|max:500',
            'city_id' => 'required|exists:cities,id',
        ];
    }

}
