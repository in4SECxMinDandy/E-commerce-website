<?php

namespace App\Http\Requests\Chat;

use App\Rules\ValidImageMagicBytes;
use Illuminate\Foundation\Http\FormRequest;

class UploadChatImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'integer', 'exists:chat_sessions,id'],
            'image' => ['required', 'file', 'max:5120', new ValidImageMagicBytes()],
        ];
    }
}
