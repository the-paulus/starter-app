<?php

namespace App\Http\Controllers;

use Grpc\Server;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tymon\JWTAuth\JWTAuth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    private $user;
    private $token;

    public function __construct()
    {
    }

    public function authenticated(Request $request) {

        JWTAuth::parseToken();

        if(JWTAuth::toUser() === FALSE) {
            throw new AccessDeniedHttpException('Received uninstantiated user error.');
        }

        return redirect(config('app.frontend_url', $request->server->all()) . '/token/' . JWTAuth::getToken());

    }

    public function unauthorized()  {

        throw new AccessDeniedHttpException('You do not have access to this application');

    }

    public function logout() {

        return 'Successfully logged out.';

    }

    public function emulateUser($id) {

        $this->user = User::findOrFail($id);

        $this->authorize('emulate', $this->user);

        $this->token = JWTAuth::customClaims(['emu' => true])->fromUser($this->user);

        return response()->json(['token' => $this->token, 'forceTokenReload' => TRUE], Response::HTTP_OK)->header('Authorization', ('Bearer '.$this->token));
    }

    public function index()
    {

    }

    public function show($id)
    {

    }

    public function store(Request $request)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}
