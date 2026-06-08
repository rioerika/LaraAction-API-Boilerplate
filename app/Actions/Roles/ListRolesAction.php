<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListRolesAction
{
    public function handle(): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions:id,name,guard_name')
            ->latest('id')
            ->paginate(perPage: 15);
    }
}
