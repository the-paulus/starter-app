<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PermissionPolicy
 * @package App\Policies
 */
class PermissionPolicy
{
    use HandlesAuthorization;

    public function __call($name, $arguments)
    {

        if( count($arguments) != 2 ) {

            throw new \BadMethodCallException('Permission policy requires 2 arguments, user and model.');

        } else {

            list($user, $model) = $arguments;

            if( method_exists($this, $name) ) {

                return $this->$name($user, $model);

            } else {

                throw new \BadMethodCallException('Policy method not found: ' . $name);
            }
        }
    }

    /**
     * Dynamically gets the permission of the model.
     *
     * @param Model     $model      Model object to inspect.
     * @param string    $permission Permission to check for.
     *
     * @return string   Calculated permission string.
     */
    private function getTargetModelPermission($model, $permission) {

        $class = class_basename(is_string($model) ? $model : get_class($model));

        return $class::getModelPermission($permission);

    }

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
        return $user->hasPermission($this->getTargetModelPermission($model, 'access'));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User   $user
     *
     * @return bool
     */
    public function create(User $user)
    {

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User   $user
     * @param  Model  $model
     *
     * @return bool
     */
    public function update(User $user, User $model)
    {

        return $user->hasPermission($this->getTargetModelPermission($model, 'modify'));

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User   $user
     * @param  Model  $model
     *
     * @return bool
     */
    public function delete(User $user, User $model)
    {

        return $user->hasPermission($this->getTargetModelPermission($model, 'delete'));

    }
}
