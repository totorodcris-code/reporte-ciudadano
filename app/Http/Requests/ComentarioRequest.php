<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class ComentarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Temporalmente sin validación para aislar el problema
            // 'contenido' => 'required|string|max:1000',
            // 'incidencia_id' => 'required|exists:incidencias,id',
            // 'parent_id' => 'nullable|exists:comentarios,id',
            // 'user_id' => 'sometimes|exists:users,id'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contenido.required' => 'El contenido del comentario es requerido',
            'contenido.string' => 'El contenido debe ser texto',
            'contenido.max' => 'El contenido no puede exceder 1000 caracteres',
            'incidencia_id.required' => 'La incidencia es requerida',
            'incidencia_id.exists' => 'La incidencia seleccionada no existe',
            'parent_id.exists' => 'El comentario padre no existe'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return JsonResponse
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422);
    }
}
