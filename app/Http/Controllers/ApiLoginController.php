<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class ApiLoginController extends Controller
{

    private $user;

    public function __construct()
    {

        $this->user = new User;

    }

    public function login(Request $request) {

        $credentials = $request->only(['email', 'password']);

        //$credentials['password'] = Hash::make($credentials['password']);

        try {

            if( !$token = JWTAuth::attempt($credentials) ) {

                return response()->json(['data' => [], 'errors' => ['Invalid Credentials']], Response::HTTP_UNAUTHORIZED);

            }

        } catch( JWTException $JWTException ) {

            return response()->json(['data' => [], 'errors' => [$JWTException->getMessage()]], Response::HTTP_BAD_REQUEST);

        }

        return response()->json(compact('token'));

    }

    public function getAuthUser(Request $request) {

        $user = JWTAuth::toUser($request->token);

        return response()->json(['data' => ['user' => $user]]);

    }
}
