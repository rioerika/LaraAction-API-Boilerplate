<?php

declare(strict_types=1);

namespace App\Actions\Permissions;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListPermissionsAction
{
    public function handle(): LengthAwarePaginator
    {
        return Permission::query()
            ->with('roles:id,name,guard_name')
            ->latest('id')
            ->paginate(perPage: 15);
    }
}
