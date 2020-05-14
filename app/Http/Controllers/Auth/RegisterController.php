<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\User;
use App\Setting;
use App\Sekolah;
use App\Server;
use App\Event;
use App\Peserta_event;
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
    protected $redirectTo = RouteServiceProvider::HOME;

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
    /*
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
    */
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    /*
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
    */
    protected function getTimezone(Request $request)
    {
        if ($timezone = $request->get('tz')) {
            return $timezone;
        }

        // fetch it from FreeGeoIp
        try {
            $response = Http::get('https://freegeoip.app/json/');
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
            $setting = Setting::where('key', 'url_server')->first();
            $timezone =  $this->getTimezone($request);
            $arguments = [
                'npsn' => $request->npsn,
                'lisensi' => $request->lisensi
            ];
            $id_server = explode('-', $request->npsn);
            if(count($id_server) > 1){
                $host_server = $setting->value.'validasi-kode';
            } else {
                $host_server = $setting->value.'validasi';
            }
            $response = Http::asForm()->post($host_server, $arguments);
            if($response->status() == 200){
                $body = $response->json();
                if($body['success']){
                    if(count($id_server) > 1){
                        $event = $body['data'];
                        $peserta = $event['peserta'];
                        unset($event['peserta']);
                        $insert_event = Event::updateOrCreate($event);
                        foreach($peserta as $pes){
                            Sekolah::updateOrCreate($pes['sekolah']);
                            unset($pes['sekolah']);
                            $create_peserta = Peserta_event::updateOrCreate($pes);
                        }
                        $create_user = User::firstOrCreate(
                            [ 
                                'name' => $insert_event->nama,
                                'username' => $insert_event->kode,
                                'email' => strtolower(str_replace(' ', '', $insert_event->kode)).'@cyberelectra.co.id',
                            ],
                            [
                                'email_verified_at' => now(),
                                'password' => app('hash')->make($insert_event->password),
                                'timezone' => $timezone,
                                'menuroles' => 'proktor'
                            ]
                        );
                        $user->attachRole('proktor');
                    } else {
                        $sekolah = $body['data'];
                        $servers = $sekolah['server'];
                        unset($sekolah['server']);
                        $create_sekolah = Sekolah::create($sekolah);
                        $user = User::firstOrCreate(
                            [ 
                                'name' => $create_sekolah->nama,
                                'sekolah_id' => $create_sekolah->sekolah_id,
                                'username' => $create_sekolah->npsn,
                            ],
                            [
                                'email' => $create_sekolah->email,
                                'email_verified_at' => now(),
                                'timezone' => $timezone,
                                'menuroles' => 'sekolah',
                                'password' => app('hash')->make($create_sekolah->npsn),
                            ]
                        );
                        $user->attachRole('sekolah');
                        if($servers){
                            foreach($servers as $server){
                                $create_server = Server::firstOrCreate($server);
                                $user_proktor = User::firstOrCreate(
                                    [ 
                                        'name' => $create_server->id_server,
                                        'sekolah_id' => $create_server->sekolah_id,
                                        'username' => $create_server->id_server,
                                    ],
                                    [
                                        'email' => strtolower(str_replace(' ', '', $create_server->id_server)).'@cyberelectra.co.id',
                                        'email_verified_at' => now(),
                                        'password' => app('hash')->make($create_server->password),
                                        'timezone' => $timezone,
                                        'menuroles' => 'proktor',
                                    ]
                                );
                                $user_proktor->attachRole('proktor');
                            }
                        }
                    }
                    return redirect('/login')->with('success', 'Aktivasi Lisensi berhasil. Silahkan login menggunakan <b>ID Server</b> dan <b>password</b> yang sudah dibuat di server');
                } else {
                    return redirect()->back()->withInput($request->except(['_token']))->with('error', $body['message']);
                }
            } else {
                return redirect()->back()->withInput($request->except(['_token']))->with('error', 'Server tidak merespon');
            }
        }
    }
}
