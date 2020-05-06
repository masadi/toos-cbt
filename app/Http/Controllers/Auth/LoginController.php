<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Sekolah;
use App\Server;
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function showLoginForm()
    {
        $sekolah = Sekolah::first();
        if(!$sekolah){
            return redirect()->route('register');
        }
        $server = Server::where('status', 1)->first();
        $status = '';
        if(!$server){
			$status = '<div class="alert alert-danger" role="alert">Server belum diaktifkan. Silahkan hubungi proktor</div>';
		}
        return view('auth.login', compact('status', 'server'));
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required|min:5'
        ]);

        $email = $request->get('email');
        $password = $request->get('password');
        $remember_me = 1;

        $login_type = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$login_type => $email, 'password' => $password], $remember_me)) {
            //Auth successful here
            $user = Auth::user();
            if(!$user->logout){
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'login_error' => 'Maaf, username sedang aktif. Silahkan hubungi proktor untuk me-reset login',
                ]);
            } else {
                return redirect('/');
            }
        }
        return redirect()->back()->withInput()->withErrors([
                'login_error' => 'Email/Username dan password salah.',
            ]);
    }
}
