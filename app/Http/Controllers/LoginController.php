<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Auth;
use Helper;
use App\User;
use App\Sekolah;
use App\Server;
use App\Rombongan_belajar;
use App\Ptk;
use App\Event;
class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/';
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected function getTimezone(Request $request)
    {
        if ($timezone = $request->get('tz')) {
            return $timezone;
        }

        // fetch it from FreeGeoIp
        try {
            $client = new Client(); //GuzzleHttp\Client
            $url = 'https://freegeoip.app/json/';
            $curl = $client->get($url);
            $response = json_decode($curl->getBody());
            return $response->time_zone;
        } catch (\Exception $e) {}
    }
    public function sekolah()
    {
        $sekolah = Sekolah::first();
		if(!$sekolah){
			return redirect('/register');
        }
        return redirect('/login-proktor');
        $server = Server::with('sekolah')->first();
		return view('auth.login-sekolah', compact('server'));
    }
    public function proktor()
    {
        $sekolah = Sekolah::first();
		if(!$sekolah){
			return redirect('/register');
		}
        $sn = Helper::UniqueMachineID();
        $internet = Helper::internet();
        $server = Server::with('sekolah')->first();
        if($server){
            $internet = 1;
        }
		return view('auth.login-proktor', compact('sn', 'internet', 'server'));
    }
    public function login_sekolah(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        $email = $request->get('email');
        $password = $request->get('password');
        $remember_me = $request->remember;

        $login_type = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$login_type => $email, 'password' => $password], $remember_me)) {
            //Auth successful here
            return redirect()->intended($this->redirectPath());
        }

        return redirect()->back()
            ->withInput()
            ->withErrors([
                'login_error' => 'Email/Username dan password salah.',
            ]);
    }
    public function login_proktor(Request $request){
        $messages = [
            'id_server.required'	=> 'ID Server tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 5 karakter',
        ];
        $validator = Validator::make($request->all(), [
            'id_server' => ['required'],
            'password' => ['required', 'min:5'],
        ],
            $messages
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $event = Event::where('kode', $request->id_server)->first();
            if($event){
                $email = $request->id_server;
                $password = $request->password;
                $remember_me = 1;
                $login_type = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
                if (Auth::attempt([$login_type => $email, 'password' => $password], $remember_me)) {                        //Auth successful here
                    return redirect('/');//->intended($this->redirectPath());
                } else {
                    return redirect()->back()->withInput($request->except(['_token']))->with('error', 'ID Server/Password salah');
                }
            } else {
                return redirect()->back()->withInput($request->except(['_token']))->with('error', 'ID Server tidak terdaftar di server ini');
            }
        }
    }
}
