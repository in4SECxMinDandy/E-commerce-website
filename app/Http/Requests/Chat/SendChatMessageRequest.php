<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SendChatMessageRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->route('session') && ! $this->filled('session_id')) {
            $this->merge([
                'session_id' => $this->route('session')->id,
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required_without:route_session', 'integer', 'exists:chat_sessions,id'],
            'content' => ['nullable', 'string', 'max:5000'],
            'image_path' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->filled('content') && ! $this->filled('image_path')) {
                    $validator->errors()->add('content', 'Nội dung hoặc ảnh là bắt buộc.');
                }
            },
        ];
    }
}
