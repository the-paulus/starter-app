<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class UserController handles CRUD requests.
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{

    /**
     * @var string Default sorting order. Either ASC or DESC.
     */
    protected static $default_order = 'ASC';

    /**
     * @var int Default number of items to list per page.
     */
    protected static $default_per_page = 15;

    /**
     * @var string $default_sort Name of the field to sort on by default.
     */
    protected static $default_sort = 'last_name';

    /**
     * @var string $model Model class that the controller is bound to.
     */
    protected static $model = User::class;

    /**
     * Returns a JSON object containing the different types of allowed authentication methods.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authentication_types() {

        if(Auth::id()) {

            return response()->json(['data' => DB::table('auth_types')->get()], Response::HTTP_OK);

        } else {

            return response()->json(['data' => [], 'errors' => ['Not authorized']], Response::HTTP_UNAUTHORIZED);

        }

    }

    /**
     * Returns the current user information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo() {

        return response()->json([ 'data' => [ 'user' => Auth::user() ] ]);

    }

}
