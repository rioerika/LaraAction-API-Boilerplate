<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Users;

use App\Http\Requests\Api\ApiFormRequest;

final class UserRolesSyncRequest extends ApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', 'exists:roles,name'],
        ];
    }
}
