<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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

            'lastname1' => 'required|string|min:3|max:255',

            'lastname2' => 'required|string|min:3|max:255',

            'email' => 'required|string|email|max:255|unique:users,email',

            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los :max caracteres.',

            'lastname1.required' => 'El primer apellido es obligatorio.',
            'lastname1.string' => 'El primer apellido debe ser un texto válido.',
            'lastname1.min' => 'El primer apellido debe tener al menos :min caracteres.',
            'lastname1.max' => 'El primer apellido no puede superar los :max caracteres.',

    
            'lastname2.required' => 'El segundo apellido es obligatorio.',
            'lastname2.string' => 'El segundo apellido debe ser un texto válido.',
            'lastname2.min' => 'El segundo apellido debe tener al menos :min caracteres.',
            'lastname2.max' => 'El segundo apellido no puede superar los :max caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser un texto válido.',
            'email.email' => 'Introduce un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede superar los :max caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser un texto válido.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'lastname1' => 'primer apellido',
            'lastname2' => 'segundo apellido',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }
}
