<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'price_unit_id' => 'required|exists:product_prices_units,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }

     public function messages(): array
    {
        return [
            'product_id.required' => 'El producto es obligatorio.',
            'product_id.exists' => 'El producto seleccionado no existe.',

            'price_unit_id.required' => 'La unidad de precio es obligatoria.',
            'price_unit_id.exists' => 'La unidad de precio seleccionada no existe.',

            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'La imagen debe ser de tipo: jpg, jpeg, png o webp.',
            'image.max' => 'La imagen no puede superar los 4MB.',
        ];
    }

  
    public function attributes(): array
    {
        return [
            'product_id' => 'producto',
            'price_unit_id' => 'unidad de precio',
            'image' => 'imagen',
        ];
    }

}
