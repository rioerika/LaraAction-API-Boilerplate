<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Permissions;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rule;

final class StorePermissionRequest extends ApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'guard_name' => ['nullable', Rule::in(['sanctum'])],
        ];
    }
}
