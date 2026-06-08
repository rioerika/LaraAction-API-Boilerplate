<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DeleteUserAction
{
    public function handle(User $user): void
    {
        DB::transaction(static function () use ($user): void {
            $user->tokens()->delete();
            $user->syncRoles([]);
            $user->syncPermissions([]);
            $user->delete();
        });
    }
}
