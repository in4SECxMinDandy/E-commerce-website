<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(config('universaltea.roles.admin')) ?? false;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
