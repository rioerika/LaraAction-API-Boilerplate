<?php

declare(strict_types=1);

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{
    protected string $guard_name = 'sanctum';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];
}
