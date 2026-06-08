<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateUserAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): User
    {
        DB::transaction(static function () use ($user, $data): void {
            $user->fill($data);
            $user->save();
        });

        return $user->load(['roles:id,name,guard_name', 'permissions:id,name,guard_name']);
    }
}
