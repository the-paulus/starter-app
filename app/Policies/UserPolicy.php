<?php

namespace App\Policies;

use App\Models\User;

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

}
