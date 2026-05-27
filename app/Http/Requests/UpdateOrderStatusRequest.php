<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                'in:Pendiente,Procesando,Completado,Cancelado',
            ],
        ];
    }

    /**
     * Custom messages
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Debes seleccionar un estado.',
            
            'status.string' => 'El estado debe ser un texto válido.',

            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }

    
    public function attributes(): array
    {
        return [
            'status' => 'estado del pedido',
        ];
    }
}
