<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListUsersAction
{
    public function handle(): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles:id,name,guard_name', 'permissions:id,name,guard_name'])
            ->latest('id')
            ->paginate(perPage: 15);
    }
}
