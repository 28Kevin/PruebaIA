<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:2000',
            'metadata' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'El contenido del mensaje es obligatorio.',
            'content.string' => 'El contenido del mensaje debe ser una cadena de texto.',
            'content.max' => 'El mensaje no puede exceder los 2000 caracteres.',
            'metadata.array' => 'Los metadatos deben ser un objeto válido.',
        ];
    }
}
