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

    /**
     * @var string $default_sort Name of the field to sort on by default.
     */
    protected static $default_sort = 'name';

    /**
     * @var string Default sorting order. Either ASC or DESC.
     */
    protected static $default_order = 'ASC';

}
