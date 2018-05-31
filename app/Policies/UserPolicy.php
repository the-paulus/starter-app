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
     * Determine whether the user can delete the model.
     *
     * @param  User   $user     User object to perform check on.
     * @param  Model  $model    The model they are performing the task on.
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

}
