<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/plateforme/home';
    protected function redirectTo()
    {
        $user = Auth::guard('user')->user();

        if ($user->isPlateforme()) {
            return '/plateforme/home';
        } elseif ($user->isProject()) {
            return '/project/home';
        } elseif ($user->isGroup()) {
            return '/group/home';
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('user.guest:user', ['except' => 'logout']);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('user');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {

        return view('tenant.auth.login');
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        $homeRoute = $this->getHomeRoute(); // Get the appropriate home route based on user's role

        $this->guard('user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Regenerate the CSRF token

        return $this->loggedOut($request) ?: redirect()->route($homeRoute);
    }


    protected function getHomeRoute()
    {
        $user = Auth::guard('user')->user();

        if ($user->isPlateforme()) {
            return 'tenant.plateforme.home';
        } elseif ($user->isProject()) {
            return 'tenant.project.home';
        } elseif ($user->isGroup()) {
            return 'tenant.group.home';
        }
    }
}