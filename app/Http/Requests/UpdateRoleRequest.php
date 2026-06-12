<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar_roles_permisos') ?? false;
    }

    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('roles', 'name')->ignore($role?->id),
            ],
            'permission' => ['required', 'array', 'min:1'],
            'permission.*' => ['integer', Rule::exists('permissions', 'id')->where('guard_name', 'web')],
        ];
    }
}