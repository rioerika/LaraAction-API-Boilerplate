<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class LoginAction
{
    /**
     * @param  array{email:string,password:string,device_name?:string|null}  $credentials
     * @return array<string, mixed>
     */
    public function handle(array $credentials): array
    {
        $user = User::query()
            ->with(['roles:id,name,guard_name', 'permissions:id,name,guard_name'])
            ->where('email', $credentials['email'])
            ->first();

        if (! $user instanceof User || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($credentials['device_name'] ?? 'api-token')->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }
}
