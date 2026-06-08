<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Permissions;

use App\Http\Requests\Api\ApiFormRequest;
use App\Models\Permission;
use Illuminate\Validation\Rule;

final class UpdatePermissionRequest extends ApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Permission $permission */
        $permission = $this->route('permission');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($permission)],
            'guard_name' => ['sometimes', Rule::in(['sanctum'])],
        ];
    }
}
