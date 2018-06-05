<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserGroup;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserGroupPolicy extends ApplicationPolicy
{

    protected static $model = UserGroup::class;

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

        if( $model->users()->count() ) {

            throw new HttpException(Response::HTTP_BAD_REQUEST, json_encode(['data' => [], 'errors' => ['Group not empty.']]));

        }

        return parent::delete($user, $model);

    }
}
