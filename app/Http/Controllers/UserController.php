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






}
