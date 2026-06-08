<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Roles;

use App\Http\Requests\Api\ApiFormRequest;
use App\Models\Role;
use Illuminate\Validation\Rule;

final class UpdateRoleRequest extends ApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role)],
            'guard_name' => ['sometimes', Rule::in(['sanctum'])],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['required', 'string', 'exists:permissions,name'],
        ];
    }
}
