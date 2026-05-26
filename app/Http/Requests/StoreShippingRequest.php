<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingRequest extends FormRequest
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
            'shipping_address' => 'required|string|min:5|max:255',
            'province'         => 'required|string|min:3|max:255',
            'postal_code'      => 'required|string|digits:5',
            'locality'         => 'required|string|min:2|max:255',
        ];
    }

     public function messages(): array
    {
        return [
            'shipping_address.required' => 'La dirección de envío es obligatoria.',
            'shipping_address.string'   => 'La dirección de envío debe ser un texto válido.',
            'shipping_address.max'      => 'La dirección de envío no puede superar los :max caracteres.',
            'shipping_address.min'      => 'La dirección de envío debe tener al menos :min caracteres.',

            'province.required' => 'La provincia es obligatoria.',
            'province.string'   => 'La provincia debe ser un texto válido.',
            'province.min'      => 'La provincia debe tener al menos :min caracteres.',
            'province.max'      => 'La provincia no puede superar los :max caracteres.',

            'postal_code.required' => 'El código postal es obligatorio.',
            'postal_code.string'   => 'El código postal debe ser un texto válido.',
            'postal_code.digits'   => 'El código postal debe tener exactamente :digits dígitos.',

            'locality.required' => 'La localidad es obligatoria.',
            'locality.string'   => 'La localidad debe ser un texto válido.',
            'locality.min'      => 'La localidad debe tener al menos :min caracteres.',
            'locality.max'      => 'La localidad no puede superar los :max caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'shipping_address' => 'dirección de envío',
            'province'         => 'provincia',
            'postal_code'      => 'código postal',
            'locality'         => 'localidad',
        ];
    }

}
