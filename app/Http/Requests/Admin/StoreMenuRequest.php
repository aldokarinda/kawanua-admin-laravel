<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menuId = $this->route('menu')?->id;
        $slugUnique = $menuId ? "unique:menus,slug,{$menuId}" : 'unique:menus,slug';

        return [
            'name'            => ['required', 'string', 'max:255'],
            'slug'            => ['required', 'string', $slugUnique],
            'parent_id'       => ['nullable', 'exists:menus,id'],
            'icon'            => ['nullable', 'string', 'max:255'],
            'route_name'      => ['nullable', 'string', 'max:255'],
            'url'             => ['nullable', 'string', 'max:255'],
            'permission_name' => ['nullable', 'string', 'max:255'],
            'order'           => ['integer', 'min:0'],
        ];
    }
}
