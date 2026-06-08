<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateUserAction
{
    /**
     * @param  array{name:string,email:string,password:string}  $data
     */
    public function handle(array $data): User
    {
        /** @var User $user */
        $user = DB::transaction(static fn (): User => User::query()->create($data));

        return $user->load(['roles:id,name,guard_name', 'permissions:id,name,guard_name']);
    }
}
