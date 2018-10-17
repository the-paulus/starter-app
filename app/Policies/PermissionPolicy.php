<?php

namespace App\Policies;

use App\Models\Permission;

/**
 * PermissionPolicy class defines what actions can and cannot be performed on permissions.
 *
 * @package App\Policies
 */
class PermissionPolicy extends ApplicationPolicy
{

    /**
     * @var string Class of the model the policy is for.
     */
    protected static $model = Permission::class;

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user  User object to perform check on.
     *
     * @return bool Will always return false because we don't want users creating permissions on the fly.
     */
    public function create(User $user)  {

        return FALSE;

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model they are performing the task on.
     *
     * @return bool Will always return false because we don't want users updating permissions on the fly.
     */
    public function update(User $user, Model $model)  {

        return FALSE;

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model they are performing the task on.
     *
     * @return bool Will always return false because we don't want users updating permissions on the fly.
     */
    public function delete(User $user, Model $model)  {

        return FALSE;

    }
}
