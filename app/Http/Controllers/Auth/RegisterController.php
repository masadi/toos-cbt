<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Sekolah;
use App\Server;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login-sekolah';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
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
    public function register(Request $request){
        $messages = [
            'npsn.required'	=> 'NPSN tidak boleh kosong',
            //'email.required' => 'Email tidak boleh kosong',
            //'email.unique' => 'Email sudah terdaftar',
            'lisensi.required' => 'Lisensi tidak boleh kosong',
            //'password.required' => 'Password tidak boleh kosong',
            //'password.confirmed' => 'Konfirmasi password salah',
        ];
        $validator = Validator::make($request->all(), [
            'npsn' => ['required', 'string', 'max:255'],
            'lisensi' => ['required', 'string', 'max:255'],
            //'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            //'password' => ['required', 'string', 'min:8', 'confirmed'],
        ],
            $messages
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $timezone =  $this->getTimezone($request);
            $arguments = [
                'npsn' => $request->npsn,
                'lisensi' => $request->lisensi
            ];
            $host_server = config('global.url_server').'validasi';
            $client = new Client(); //GuzzleHttp\Client
            $curl = $client->post($host_server, [	
                'form_params' => $arguments
            ]);
            if($curl->getStatusCode() == 200){
                $response = json_decode($curl->getBody());
                if($response->success){
                    $sekolah = $response->data;
                    $data_server = $sekolah->server;
                    unset($sekolah->server);
                    $array = (array) $sekolah;
                    Sekolah::create($array);
                    $user = User::create([ 
                        'name' => $sekolah->nama,
                        'sekolah_id' => $sekolah->sekolah_id,
                        'username' => strtolower(str_replace(' ', '', $sekolah->nama)),
                        'email' => $sekolah->email,
                        'email_verified_at' => now(),
                        'password' => app('hash')->make($sekolah->npsn),
                        'timezone' => $timezone,
                        'menuroles' => 'sekolah'
                    ]);
                    $user->assignRole('sekolah');
                    if($data_server){
                        foreach($data_server as $server){
                            unset($server->created_at, $server->updated_at);
                            $array_server = (array) $server;
                            Server::firstOrCreate($array_server);
                            $user = User::where('name', $server->id_server)->first();
                            if(!$user){
                                $user = User::firstOrCreate([ 
                                    'name' => $server->id_server,
                                    'sekolah_id' => $server->sekolah_id,
                                    'username' => $server->id_server,
                                    'email' => strtolower(str_replace(' ', '', $server->id_server)).'@cyberelectra.co.id',
                                    //'email_verified_at' => now(),
                                    'password' => app('hash')->make($server->password),
                                    'timezone' => $timezone,
                                    'menuroles' => 'proktor'
                                ]);
                                $user->assignRole('proktor');
                            }
                        }
                    }
                    //return redirect('/login-proktor')->with('success', 'Aktivasi Lisensi berhasil. Silahkan login menggunakan username <b>'.$user->username.'</b> dan password <b>'.$sekolah->npsn.'</b>');
                    return redirect('/login-proktor')->with('success', 'Aktivasi Lisensi berhasil. Silahkan login menggunakan <b>ID Server</b> dan <b>password</b> yang sudah dibuat di server');
                } else {
                    return redirect()->back()->withInput($request->except(['_token']))->with('error', $response->message);
                }
            } else {
                return redirect()->back()->withInput($request->except(['_token']))->with('error', 'Server tidak merespon');
            }
        }
    }
}
