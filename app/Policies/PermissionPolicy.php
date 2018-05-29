<?php

namespace App\Policies;

use App\Models\Permission;

/**
 * Class PermissionPolicy
 * @package App\Policies
 */
class PermissionPolicy extends ApplicationPolicy
{

    protected static $model = Permission::class;

}
