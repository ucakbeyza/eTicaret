<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'name' => 'required|string|min:3',
            'slug' => 'required|string|unique:products,slug',
            'description' => 'required|string|min:20',
            'price' => 'required|numeric',
            'currency' => 'required|string',
            'stock' => 'required|integer',
            'sku' => 'required|string',
            'brand' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'status' => 'required|string',
        ];
    }
}
