<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $permissionId = $this->route('permission')?->id;
        $unique = $permissionId ? "unique:permissions,name,{$permissionId}" : 'unique:permissions,name';

        return [
            'name' => ['required', 'string', $unique],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A permission with this name already exists.',
        ];
    }
}
