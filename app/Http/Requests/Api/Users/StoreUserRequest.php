<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Users;

use App\Http\Requests\Api\ApiFormRequest;
use Illuminate\Validation\Rules\Password;

final class StoreUserRequest extends ApiFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
