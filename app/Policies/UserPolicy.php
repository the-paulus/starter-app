<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class UserPolicy defines policies specifically relating to performing actions on User models.
 *
 * This is a separate class because it may require more specialized code in the future to handle new features.
 *
 * @package App\Policies
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model they are performing the task on.
     *
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return $user->hasPermission('access');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission('create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model they are performing the task on.
     *
     * @return bool
     */
    public function update(User $user, User $model)
    {
        return $user->hasPermission('modify');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model they are performing the task on.
     *
     * @return bool
     */
    public function delete(User $user, User $model)
    {
        return $user->hasPermission('delete');
    }
}
