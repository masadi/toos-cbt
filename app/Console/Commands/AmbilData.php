<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Event;
use App\Server;
use App\Sekolah;
use App\Peserta_event;
use App\Ptk;
use App\Jurusan_sp;
use App\Rombongan_belajar;
use App\Pembelajaran;
use App\Peserta_didik;
use App\Exam;
use App\Question;
use App\Answer;
use App\Anggota_rombel;
use App\User;
use App\User_exam;
use App\User_question;
use App\Ujian;
use App\Mata_pelajaran;
use Faker\Factory as Faker;
use Str;
use Helper;
use Illuminate\Support\Facades\DB;
use Delight\Random\Random;
class AmbilData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ambil:data {username} {data} {offset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cbt_server = config('cbt.server');
        $username = $this->argument('username');
        $get_tz = User::where('username', $username)->first();
        $data = $this->argument('data');
        $offset = $this->argument('offset');
        $event = Event::where('kode', $username)->with('peserta.sekolah')->first();
        if($event){
            if($cbt_server){
                $event_server = Event::on('pgsql')->with('peserta.sekolah')->find($event->id);
                foreach($event_server->peserta as $peserta){
                    $sekolah_id[] = $peserta->sekolah->sekolah_id;
                }
                if($data == 'ptk'){
                    Ptk::on('pgsql')->with(['user', 'sekolah'])->whereIn('sekolah_id', $sekolah_id)->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_sekolah($item->sekolah);
                            $this->insert_ptk($item, $get_tz->timezone);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' rombongan_belajar 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'rombongan_belajar','offset' => 0]);
                } elseif($data == 'rombongan_belajar'){
                    Rombongan_belajar::on('pgsql')->with(['sekolah', 'ptk','jurusan_sp'])->where(function($query) use ($sekolah_id){
                        $query->whereIn('sekolah_id', $sekolah_id);
                    })->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_sekolah($item->sekolah);
                            $this->insert_ptk($item, $get_tz->timezone);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' ujian 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'ujian','offset' => 0]);
                } elseif($data == 'ujian'){
                    Ujian::on('pgsql')->with('mata_pelajaran')->where('event_id', $event->id)->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_ujian($item);
                            $this->insert_mata_pelajaran($item->mata_pelajaran);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' anggota_rombel 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'anggota_rombel','offset' => 0]);
                } elseif($data == 'anggota_rombel'){
                    Anggota_rombel::on('pgsql')->whereHas('rombongan_belajar', function($query) use ($sekolah_id){
                        $query->whereIn('sekolah_id', $sekolah_id);
                    })->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_sekolah($item->sekolah);
                            $this->insert_ptk($item, $get_tz->timezone);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' exams 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'exams','offset' => 0]);
                } elseif($data == 'exams'){
                    Exam::on('pgsql')->where(function($query) use ($event){
                        $query->whereHas('event', function($query) use ($event){
                            $query->where('events.id', $event->id);
                        });
                        $query->whereValid(1);
                        //$query->whereStatus(1);
                        //$query->whereNull('sinkron');
                    })->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_sekolah($item->sekolah);
                            $this->insert_ptk($item, $get_tz->timezone);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' questions 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'questions','offset' => 0]);
                } elseif($data == 'questions'){
                    Question::on('pgsql')->whereHas('exam', function($query) use ($event){
                        $query->whereHas('event', function($query) use ($event){
                            $query->where('events.id', $event->id);
                        });
                        $query->whereValid(1);
                        //$query->whereStatus(1);
                        //$query->whereNull('sinkron');
                    })->chunk(50, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_sekolah($item->sekolah);
                            $this->insert_ptk($item, $get_tz->timezone);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' answers 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'answers','offset' => 0]);
                } elseif($data == 'answers'){
                    Answer::on('pgsql')->whereHas('question', function($query) use ($event){
                        $query->whereHas('exam', function($sq) use ($event){
                            //$sq->whereStatus(1);
                            $sq->whereValid(1);
                            //$sq->whereNull('sinkron');
                            $sq->whereHas('event', function($ssq) use ($event){
                                $ssq->where('events.id', $event->id);
                            });
                        });
                    })->chunk(50, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_sekolah($item->sekolah);
                            $this->insert_ptk($item, $get_tz->timezone);
                        }
                    });
                }
            } else {
                $host_server = config('global.url_server').'proses-download-event';
                $arguments = [
                    'data' => $data,
                    'offset' => $offset,
                    'event_id' => $event->id,
                ];
                $client = new Client(); //GuzzleHttp\Client
                $curl = $client->post($host_server, [	
                    'curl.options' => [
                        'CURLOPT_BUFFERSIZE' => '120000L'
                    ],
                    ['verify' => false],
                    'form_params' => $arguments
                ]);
                if($curl->getStatusCode() == 200){
                    $output = json_decode($curl->getBody());
                    $this->call('proses:sync', ['query' => 'download', 'data' => $output, 'timezone' => $get_tz->timezone]);
                    sleep(1);
                    if($output){
                        if($output->response['next']){
                            $this->info('Start again => ambil:data '.$username.' '.$data.' '.$output->response['offset']. ' Jumlah ('.$output->response['count'].')');
                            $this->call('ambil:data', ['username' => $username, 'data' => $output->response['next'],'offset' => $output->response['offset']]);
                        }
                    }
                }
            }
        } else {
            $server = Server::where('id_server', $username)->first();
            if($cbt_server && $server){
                if($data == 'ptk'){
                    Ptk::on('pgsql')->with(['user', 'sekolah'])->where('sekolah_id', $server->sekolah_id)->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_sekolah($item->sekolah);
                            $this->insert_ptk($item, $get_tz->timezone);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' rombongan_belajar 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'rombongan_belajar','offset' => 0]);
                } elseif($data == 'rombongan_belajar'){
                    Rombongan_belajar::on('pgsql')->with(['ptk','jurusan_sp'])->where(function($query) use ($server){
                        $query->where('server_id', $server->server_id);
                    })->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            if(isset($item->sekolah)){
                                $this->insert_sekolah($item->sekolah);
                            }
                            $this->insert_ptk($item->ptk, $get_tz->timezone);
                            $this->insert_jurusan_sp($item->jurusan_sp);
                            $this->insert_rombel($item);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' pembelajaran 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'pembelajaran','offset' => 0]);
                } elseif($data == 'pembelajaran'){
                    Pembelajaran::on('pgsql')->with(['ptk', 'rombongan_belajar', 'mata_pelajaran'])->whereHas('rombongan_belajar', function($query) use ($server){
                        $query->where('server_id', $server->server_id);
                    })->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_ptk($item->ptk, $get_tz->timezone);
                            $this->insert_rombel($item->rombongan_belajar);
                            $this->insert_mata_pelajaran($item->mata_pelajaran);
                            $this->insert_pembelajaran($item);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' anggota_rombel 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'anggota_rombel','offset' => 0]);
                } elseif($data == 'anggota_rombel'){
                    Anggota_rombel::on('pgsql')->with('peserta_didik.user')->whereHas('rombongan_belajar', function($query) use ($server){
                        $query->where('server_id', $server->server_id);
                    })->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_peserta_didik($item->peserta_didik, $get_tz->timezone);
                            $this->insert_anggota_rombel($item);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' exams 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'exams','offset' => 0]);
                } elseif($data == 'exams'){
                    Exam::on('pgsql')->where(function($query) use ($server){
                        $query->whereHas('pembelajaran', function($sq) use ($server){
                            $sq->whereHas('rombongan_belajar', function($ssq) use ($server){
                                $ssq->where('server_id', $server->server_id);
                            });
                        });
                        $query->whereValid(1);
                        //$query->whereStatus(1);
                        //$query->whereNull('sinkron');
                    })->chunk(200, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_exam($item);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' questions 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'questions','offset' => 0]);
                } elseif($data == 'questions'){
                    Question::on('pgsql')->whereHas('exam', function($query) use ($server){
                        $query->whereHas('pembelajaran', function($sq) use ($server){
                            $sq->whereHas('rombongan_belajar', function($ssq) use ($server){
                                $ssq->where('server_id', $server->server_id);
                            });
                        });
                        $query->whereValid(1);
                        //$query->whereStatus(1);
                        //$query->whereNull('sinkron');
                    })->chunk(50, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_question($item);
                        }
                    });
                    $this->info('Start again => ambil:data '.$username.' answers 0');
                    $this->call('ambil:data', ['username' => $username, 'data' => 'answers','offset' => 0]);
                } elseif($data == 'answers'){
                    Answer::on('pgsql')->whereHas('question', function($query) use ($server){
                        $query->whereHas('exam', function($sq) use ($server){
                            //$sq->whereStatus(1);
                            $sq->whereValid(1);
                            //$sq->whereNull('sinkron');
                            $sq->whereHas('pembelajaran', function($ssq) use ($server){
                                $ssq->whereHas('rombongan_belajar', function($sssq) use ($server){
                                    $sssq->where('server_id', $server->server_id);
                                });
                            });
                        });
                    })->chunk(50, function ($result) use ($data, $get_tz){
                        foreach($result as $re){
                            $item = json_decode(json_encode($re));
                            $this->insert_answer($item);
                        }
                    });
                }
            } else {
                $host_server = config('global.url_server').'proses-download';
                $arguments = [
                    'data' => $data,
                    'offset' => $offset,
                    'server_id' => $server->server_id,
                ];
                $client = new Client(); //GuzzleHttp\Client
                $curl = $client->post($host_server, [
                    'curl.options' => [
                        'CURLOPT_BUFFERSIZE' => '120000L'
                    ],
                    ['verify' => false],
                    'form_params' => $arguments
                ]);
                if($curl->getStatusCode() == 200){
                    $output = json_decode($curl->getBody());
                    $this->call('proses:sync', ['query' => 'download', 'data' => $output, 'timezone' => $get_tz->timezone]);
                    sleep(1);
                    if($output){
                        if($output->response['next']){
                            $this->info('Start again => ambil:data '.$username.' '.$output->response['next'].' '.$output->response['offset']. ' Jumlah ('.$output->response['count'].')');
                            $this->call('ambil:data', ['username' => $username, 'data' => $output->response['next'],'offset' => $output->response['offset']]);
                        }
                    }
                }
            }
        }
    }
    private function insert_mata_pelajaran($item){
        if($item){
            Mata_pelajaran::updateOrCreate(
                [
                    'mata_pelajaran_id' => $item->mata_pelajaran_id,
                ],
                [
                    'nama' => $item->nama,
                    'pilihan_sekolah' => 1,
                    'pilihan_buku' => 1,
                    'pilihan_kepengawasan' => 1,
                    'pilihan_evaluasi' => 1,
                ]
            );
        }
    }
    private function insert_sekolah($item){
        Sekolah::updateOrCreate(
            [
                'sekolah_id' => $item->sekolah_id,
            ],
            [
                'npsn' => $item->npsn,
                'nama' => $item->nama,
                'bentuk_pendidikan_id' => $item->bentuk_pendidikan_id,
                'alamat' => $item->alamat,
                'desa_kelurahan' => $item->desa_kelurahan,
                'kecamatan' => $item->kecamatan,
                'kabupaten' => $item->kabupaten,
                'provinsi' => $item->provinsi,
                'kode_wilayah' => $item->kode_wilayah,
                'kode_pos' => $item->kode_pos,
                'no_telp' => $item->no_telp,
                'email' => $item->email,
                'website' => $item->website,
                'status_sekolah' => $item->status_sekolah,
                'lisensi' => $item->lisensi
            ]
        );
        $event = Event::first();
        if($event){
            Peserta_event::updateOrCreate([
                'event_id' => $event->id,
                'sekolah_id' => $item->sekolah_id,
            ]);
        }
    }
    private function insert_ujian($item){
        Ujian::updateOrCreate(
            [
                'id' => $item->id
            ],
            [
                'event_id' => $item->event_id,
                'mata_pelajaran_id' => $item->mata_pelajaran_id,
                'tanggal' => $item->tanggal,
            ]
        );
    }
    private function insert_ptk($item, $timezone){
        if($item){
            $password = Random::alphanumericLowercaseString(8);
            Ptk::updateOrCreate(
                [
                    'ptk_id' => $item->ptk_id
                ],
                [
                    'sekolah_id' => $item->sekolah_id,
                    'nama' => $item->nama,
                    'nuptk' => $item->nuptk,
                    'nip' => $item->nip,
                    'jenis_kelamin' => $item->jenis_kelamin,
                    'tempat_lahir' => $item->tempat_lahir,
                    'tanggal_lahir' => $item->tanggal_lahir,
                    'nik' => $item->nik,
                    'agama_id' => $item->agama_id,
                    'alamat' => $item->alamat,
                    'no_hp' => $item->no_hp,
                    'email' => $item->email,
                    'photo' => $item->photo,
                ]
            );
            if(isset($item->user)){
                $find = User::where('ptk_id', $item->user->ptk_id)->first();
                if(!$find){
                    $user = User::updateOrCreate(
                        [
                            'ptk_id' => $item->user->ptk_id, 
                        ],
                        [
                            'name' => $item->user->name,
                            'sekolah_id' => $item->user->sekolah_id,
                            'username' => $item->user->username,
                            'email' => $item->user->email,
                            'email_verified_at' => now(),
                            'password' => app('hash')->make($password),
                            'timezone' => $timezone,
                            'remember_token' => Str::random(10),
                            'menuroles' => 'ptk',
                            'default_password' => $password,
                        ]
                    );
                    if(!$user->hasRole('ptk')){
                        $user->attachRole('ptk');
                    }
                } else {
                    $user = User::updateOrCreate(
                        [
                            'ptk_id' => $item->ptk_id, 
                        ],
                        [
                            'name' => $item->nama,
                            'sekolah_id' => $item->sekolah_id,
                            'username' => $item->nuptk,
                            'email' => $item->email,
                            'email_verified_at' => now(),
                            'password' => app('hash')->make($password),
                            'timezone' => $timezone,
                            'remember_token' => Str::random(10),
                            'menuroles' => 'ptk',
                            'default_password' => $password,
                        ]
                    );
                    if(!$user->hasRole('ptk')){
                        $user->attachRole('ptk');
                    }
                }
            }
        }
    }
    private function insert_jurusan_sp($item){
        Jurusan_sp::updateOrCreate(
            [
                'jurusan_sp_id' => $item->jurusan_sp_id,
            ],
            [
                'sekolah_id' => $item->sekolah_id,
                'jurusan_id' => $item->jurusan_id,
                'nama_jurusan_sp' => $item->nama_jurusan_sp
            ]
        );
    }
    private function insert_rombel($item){
        Rombongan_belajar::updateOrCreate(
            [
                'rombongan_belajar_id' => $item->rombongan_belajar_id,
            ],
            [
                'sekolah_id' => $item->sekolah_id,
                'tingkat' => $item->tingkat_pendidikan_id,
                'jurusan_sp_id' => $item->jurusan_sp_id,
                'semester_id' => $item->semester_id,
                'jurusan_id' => $item->jurusan_id,
                'kurikulum_id' => $item->kurikulum_id,
                'nama' => $item->nama,
                'ptk_id' => $item->ptk_id,
                'server_id' => (isset($item->server_id)) ? $item->server_id : NULL,
            ]
        );
    }
    private function insert_pembelajaran($item){
        Pembelajaran::updateOrCreate(
            [
                'pembelajaran_id' => $item->pembelajaran_id,
            ],
            [
                'sekolah_id' => $item->sekolah_id,
                'semester_id' => $item->semester_id,
                'rombongan_belajar_id' => $item->rombongan_belajar_id,
                'ptk_id' => $item->ptk_id,
                'mata_pelajaran_id' => $item->mata_pelajaran_id,
                'nama_mata_pelajaran' => $item->nama_mata_pelajaran,
            ]
        );
    }
    private function insert_peserta_didik($item, $timezone){
        $password = Random::alphanumericLowercaseString(8);
        Peserta_didik::updateOrCreate(
            [
                'peserta_didik_id' => $item->peserta_didik_id,
            ],
            [
                'sekolah_id' => $item->sekolah_id,
                'nama' => $item->nama,
                'no_induk' => $item->no_induk,
                'nisn' => $item->nisn,
                'nik' => $item->nik,
                'jenis_kelamin' => $item->jenis_kelamin,
                'tempat_lahir' => $item->tempat_lahir,
                'tanggal_lahir' => $item->tanggal_lahir,
                'agama_id' => $item->agama_id,
                'alamat' => $item->alamat,
                'no_telp' => $item->no_telp,
                'email' => $item->email,
                'photo' => $item->photo,
            ]
        );
        $find = User::where('peserta_didik_id', $item->user->peserta_didik_id)->first();
        if(!$find){
            if(isset($item->user)){
                $user = User::updateOrCreate(
                    [
                        'peserta_didik_id' => $item->user->peserta_didik_id,
                    ],
                    [
                        'name' => $item->user->name,
                        'sekolah_id' => $item->user->sekolah_id,
                        'username' => $item->user->username,
                        'email' => $item->user->email,
                        'email_verified_at' => now(),
                        'password' => app('hash')->make($password),
                        'timezone' => $timezone,
                        'remember_token' => Str::random(10),
                        'menuroles' => 'peserta_didik',
                        'default_password' => $password,
                    ]
                );
                if(!$user->hasRole('peserta_didik')){
                    $user->attachRole('peserta_didik');
                }
                //
            } else {
                $user = User::updateOrCreate(
                    [
                        'peserta_didik_id' => $item->peserta_didik_id,
                    ],
                    [
                        'name' => $item->nama,
                        'sekolah_id' => $item->sekolah_id,
                        'username' => $item->nisn,
                        'email' => $item->email,
                        'email_verified_at' => now(),
                        'password' => app('hash')->make($password),
                        'timezone' => $timezone,
                        'remember_token' => Str::random(10),
                        'menuroles' => 'peserta_didik',
                        'default_password' => $password,
                    ]
                );
                if(!$user->hasRole('peserta_didik')){
                    $user->attachRole('peserta_didik');
                }
            }
        }
    }
    private function insert_anggota_rombel($item){
        Anggota_rombel::updateOrCreate(
            [
                'anggota_rombel_id' => $item->anggota_rombel_id,
            ],
            [
                'sekolah_id' => $item->sekolah_id,
                'semester_id' => $item->semester_id,
                'rombongan_belajar_id' => $item->rombongan_belajar_id,
                'peserta_didik_id' => $item->peserta_didik_id,
                'nama_peserta_didik' => $item->nama_peserta_didik,
            ]
        );
    }
    private function insert_exam($item){
        Exam::updateOrCreate(
            [
                'exam_id' => $item->exam_id,
            ],
            [
                'pembelajaran_id' => $item->pembelajaran_id,
                'mata_pelajaran_id' => $item->mata_pelajaran_id,
                'nama' => $item->nama,
                //'start' => $item->start,
                //'end' => $item->end,
                'jumlah_soal' => $item->jumlah_soal,
                //'jumlah_opsi' => $item->jumlah_opsi,
                'durasi' => $item->durasi,
                'ujian_id' => $item->ujian_id
            ]
        );
    }
    private function insert_question($item){
        Question::updateOrCreate(
            [
                'question_id' => $item->question_id,
            ],
            [
                'exam_id' => $item->exam_id,
                'soal_ke' => $item->soal_ke,
                'question' => $item->question,
            ]
        );
    }
    private function insert_answer($item){
        Answer::updateOrCreate(
            [
                'answer_id' => $item->answer_id,
            ],
            [
                'question_id' => $item->question_id,
                'answer' => $item->answer,
                'jawaban_ke' => $item->jawaban_ke,
                'correct' => $item->correct
            ]
        );
    }
}
