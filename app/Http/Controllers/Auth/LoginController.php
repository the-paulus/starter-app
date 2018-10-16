<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * LoginController class provides the logic to handle requests for user to log in.
 *
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    private $user;
    private $token;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * User has been authenticated and needs to be returned to the home page and provided with a token.
     *
     * @param Request $request The user login request.
     * @param User $user The user that made the request.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function authenticated(Request $request, User $user)
    {

        return redirect(config('app.frontend_url') . '/home?token=' . JWTAuth::fromUser($user));

    }

    /**
     * When a user is not authorized to use the application throw AccessDeniedHttpException.
     *
     * @throws AccessDeniedHttpException
     */
    public function unauthorized()  {

        throw new AccessDeniedHttpException('You do not have access to this application');

    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request   Logout HTTP request.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }

    /**
     * Allow a user to become other user if they have permission to.
     *
     * @param integer $id  The id of the user to emulate.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function emulateUser($id) {

        $this->user = User::findOrFail($id);

        $this->authorize('emulate', $this->user);

        $this->token = JWTAuth::customClaims(['emu' => true])->fromUser($this->user);

        return response()->json(['token' => $this->token, 'forceTokenReload' => TRUE], Response::HTTP_OK)->header('Authorization', ('Bearer '.$this->token));
    }
}
