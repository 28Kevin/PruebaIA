<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'metadata.array' => 'Los metadatos deben ser un objeto válido.',
        ];
    }
}
