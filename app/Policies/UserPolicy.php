<?php

namespace App\Policies;

use Illuminate\Http\Response;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\Model;


/**
 * Class UserPolicy defines policies specifically relating to performing actions on User models.
 *
 * This is a separate class because it may require more specialized code in the future to handle new features.
 *
 * @package App\Policies
 */
class UserPolicy extends ApplicationPolicy
{

    static protected $model = User::class;

    /**
     * Determine whether the user can delete the model. This prevents a user from deleting them self.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model having the action performed on.
     *
     * @return bool
     */
    public function delete(User $user, Model $model)
    {

        if($user->id == $model->id) {

            throw new HttpException(Response::HTTP_BAD_REQUEST, json_encode(['data' => [], 'errors' => 'Cannot delete yourself.']));

        }

        return parent::delete($user, $model);
    }

    /**
     * Determine whether or not a user can update the model. This allows a user to update their own information.
     *
     * @param User  $user       User object to perform check on.
     * @param Model $model      The model having the action performed on.
     *
     * @return bool
     */
    public function update(User $user, Model $model)
    {

        if( static::$model == get_class($model) && $user->id == $model->id) {

            return true;

        }

        return parent::update($user, $model);

    }

}
