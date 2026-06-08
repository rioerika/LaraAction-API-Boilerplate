<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Roles;

use App\Http\Requests\Api\ApiFormRequest;

final class RolePermissionsSyncRequest extends ApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['required', 'string', 'exists:permissions,name'],
        ];
    }
}
