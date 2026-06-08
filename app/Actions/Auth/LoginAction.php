<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class LoginAction
{
    /**
     * @param  array{email:string,password:string,device_name:string}  $credentials
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

        $this->rotateDeviceToken($user, $credentials['device_name']);
        $this->enforceTokenLimit($user);

        $expiresAt = $this->resolveExpiration();
        $token = $user->createToken(
            name: $credentials['device_name'],
            abilities: ['*'],
            expiresAt: $expiresAt,
        )->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt?->toIso8601String(),
            'user' => $user,
        ];
    }

    private function rotateDeviceToken(User $user, string $deviceName): void
    {
        $user->tokens()
            ->where('name', $deviceName)
            ->delete();
    }

    private function enforceTokenLimit(User $user): void
    {
        $maxTokens = max((int) config('sanctum.max_tokens_per_user', 5), 1);
        $currentTokenCount = $user->tokens()->count();
        $tokensToDelete = max($currentTokenCount - $maxTokens + 1, 0);

        if ($tokensToDelete === 0) {
            return;
        }

        $tokenIds = $user->tokens()
            ->orderBy('created_at')
            ->limit($tokensToDelete)
            ->pluck('id');

        if ($tokenIds->isEmpty()) {
            return;
        }

        $user->tokens()
            ->whereIn('id', $tokenIds)
            ->delete();
    }

    private function resolveExpiration(): ?Carbon
    {
        $expiration = (int) config('sanctum.expiration', 120);

        if ($expiration <= 0) {
            return null;
        }

        return now()->addMinutes($expiration);
    }
}
