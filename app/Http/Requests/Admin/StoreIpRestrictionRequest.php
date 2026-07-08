<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreIpRestrictionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ip_address' => ['required', 'string', 'max:45'],
            'type'       => ['required', 'in:whitelist,blacklist'],
            'reason'     => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'expires_at.after' => 'The expiry date must be a future date.',
        ];
    }
}
