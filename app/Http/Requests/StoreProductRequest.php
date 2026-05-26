<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
 
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [

            'name' => 'required|string|max:255',

            'description' => 'required|string',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',

            'prices' => 'required|array|min:1',

            'prices.*.units' => 'required|string|max:255',

            'prices.*.price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => 'El nombre del producto es obligatorio.',
            'name.string' => 'El nombre del producto debe ser un texto válido.',
            'name.max' => 'El nombre del producto no puede superar los :max caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser un texto válido.',

            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'image.max' => 'La imagen no puede superar los 4MB.',

            'prices.required' => 'Debes añadir al menos un precio.',
            'prices.array' => 'El formato de precios es inválido.',
            'prices.min' => 'Debes añadir al menos un precio.',

            'prices.*.units.required' => 'Las unidades son obligatorias.',
            'prices.*.units.string' => 'Las unidades deben ser un texto válido.',
            'prices.*.units.max' => 'Las unidades no pueden superar los :max caracteres.',

            'prices.*.price.required' => 'El precio es obligatorio.',
            'prices.*.price.numeric' => 'El precio debe ser numérico.',
            'prices.*.price.min' => 'El precio no puede ser negativo.',
        ];
    }

    /**
     * Custom attributes.
     */
    public function attributes(): array
    {
        return [

            'name' => 'nombre',

            'description' => 'descripción',

            'image' => 'imagen',

            'prices' => 'precios',

            'prices.*.units' => 'unidades',

            'prices.*.price' => 'precio',
        ];
    }
}
