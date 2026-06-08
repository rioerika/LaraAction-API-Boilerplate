<?php

declare(strict_types=1);

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
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
