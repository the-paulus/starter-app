<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class UserGroupController
 *
 * @package App\Http\Controllers
 */
class UserGroupController extends Controller
{
    /**
     * @var string $model Model class that the controller is bound to.
     */
    protected static $model = UserGroup::class;


}
