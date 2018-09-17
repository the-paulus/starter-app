<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

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
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function authenticated(Request $request, User $user)
    {

        Log::info("User " . $user->email . " authenticated.");

        return redirect(config('app.frontend_url') . '/home?token=' . JWTAuth::fromUser($user));

    }

    public function unauthorized()  {

        throw new AccessDeniedHttpException('You do not have access to this application');

    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Log::info("Invalidating token and logging out " . Auth::user()->email);

        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }

    public function emulateUser($id) {

        $this->user = User::findOrFail($id);

        $this->authorize('emulate', $this->user);

        $this->token = JWTAuth::customClaims(['emu' => true])->fromUser($this->user);

        return response()->json(['token' => $this->token, 'forceTokenReload' => TRUE], Response::HTTP_OK)->header('Authorization', ('Bearer '.$this->token));
    }
}
