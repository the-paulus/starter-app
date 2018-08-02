<?php

namespace App\Http\Controllers;

use App\Models\User;

/**
 * Class UserController handles CRUD requests.
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{

    /**
     * @var string $model Model class that the controller is bound to.
     */
    protected static $model = User::class;

    /**
     * @var string $default_sort Name of the field to sort on by default.
     */
    protected static $default_sort = 'last_name';

    /**
     * @var string Default sorting order. Either ASC or DESC.
     */
    protected static $default_order = 'ASC';

}
