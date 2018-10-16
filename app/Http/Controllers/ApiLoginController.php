<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

/**
 * LoginController class provides the logic to handle requests for user to log in using the API.
 *
 * @package App\Http\Controllers\Auth
 */
class ApiLoginController extends Controller
{

    /** @var User $user The user attempting to log in. */
    private $user;

    public function __construct() {

        $this->user = new User;

    }

    /**
     * Attempts to log in a user that has made the request.
     *
     * @param Request $request  User request containing the email and password to be authenticated.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {

        $credentials = $request->only(['email', 'password']);

        try {

            if( !$token = JWTAuth::attempt($credentials) ) {

                return response()->json(['data' => [], 'errors' => ['Invalid Credentials']], Response::HTTP_UNAUTHORIZED);

            }

        } catch( JWTException $JWTException ) {

            return response()->json(['data' => [], 'errors' => [$JWTException->getMessage()]], Response::HTTP_BAD_REQUEST);

        }

        return response()->json(compact('token'));

    }

    /**
     * Returns the User object of the currently logged in.
     *
     * @param Request $request The request containing the token to use when retrieving user information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser(Request $request) {

        $user = JWTAuth::toUser($request->token);

        return response()->json(['data' => ['user' => $user]]);

    }
}
