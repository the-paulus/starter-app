<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the user group.
     *
     * @param  User        $user
     * @param  UserGroup   $userGroup
     *
     * @return bool
     */
    public function view(User $user, UserGroup $userGroup)
    {

        return $user->hasPermission(UserGroup::getModelPermission('access'));

    }

    /**
     * Determine whether the user can create user groups.
     *
     * @param  User         $user
     * @return bool
     */
    public function create(User $user)
    {

        return $user->hasPermission(UserGroup::getModelPermission('create'));

    }

    /**
     * Determine whether the user can update the user group.
     *
     * @param  User         $user
     * @param  UserGroup    $userGroup
     *
     * @return bool
     */
    public function update(User $user, UserGroup $userGroup)
    {

        return $user->hasPermission(UserGroup::getModelPermission('modify'));

    }

    /**
     * Determine whether the user can delete the user group.
     *
     * Groups can only be deleted if they are empty.
     *
     * @param  User         $user
     * @param  UserGroup    $userGroup
     *
     * @return bool
     */
    public function delete(User $user, UserGroup $userGroup)
    {

        return ( $user->hasPermission(UserGroup::getModelPermission('delete')) && $userGroup->users->isEmpty() );

    }
}
