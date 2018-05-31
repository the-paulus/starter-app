<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    protected static $model = Model::class;

    /**
     * Determine whether the user can view the model.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model they are performing the task on.
     *
     * @return bool
     */
    public function view(User $user, Model $model)
    {

        return $user->hasPermission(__FUNCTION__ . ' ' . camel_case_conversion(class_basename(static::$model)));

    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user  User object to perform check on.
     *
     * @return bool
     */
    public function create(User $user)
    {

        return $user->hasPermission(__FUNCTION__ . ' ' . camel_case_conversion(class_basename(static::$model)));

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model they are performing the task on.
     *
     * @return bool
     */
    public function update(User $user, Model $model)
    {

        return $user->hasPermission(__FUNCTION__ . ' ' . camel_case_conversion(class_basename(static::$model)));

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model they are performing the task on.
     *
     * @return bool
     */
    public function delete(User $user, Model $model)
    {

        return $user->hasPermission(__FUNCTION__ . ' ' . camel_case_conversion(class_basename(static::$model)));

    }
}
