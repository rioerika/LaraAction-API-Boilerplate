<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Roles;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rule;

final class StoreRoleRequest extends ApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'guard_name' => ['nullable', Rule::in(['sanctum'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', 'string', 'exists:permissions,name'],
        ];
    }
}
