<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Sekolah;
use App\Ptk;
use App\Rombongan_belajar;
use App\Pembelajaran;
use App\Exam;
use App\Question;
use App\Answer;
use App\Server;
use App\Anggota_rombel;
use App\User_exam;
use App\Setting;
use Carbon\Carbon;
use App\Jobs\TokenJob;
use App\User;
use Artisan;
use Validator;
use Str;
use Madzipper;
use File;
use Delight\Random\Random;
use Codedge\Updater\UpdaterManager;
use App\Event;
use App\Ujian;
class ProktorController extends Controller
{
    public function __construct()
    {
        $this->menit = 15;
    }
    public function index(Request $request){
        $user = auth()->user();
        $query = $request->route('query');
        if($query == 'status-download'){
            return $this->status_download($user);
        } elseif($query == 'get-status-download'){
            return $this->get_status_download($user);
        } elseif($query == 'hitung-server'){
            return $this->hitung_server($user);
        } elseif($query == 'daftar-peserta'){
            return $this->daftar_peserta($user);
        } elseif($query == 'daftar-ptk'){
            return $this->daftar_ptk($user);
        } elseif($query == 'status-test'){
            return $this->status_test($user);
        } elseif($query == 'status-peserta'){
            return $this->status_peserta($user);
        } elseif($query == 'reset-login'){
            return $this->reset_login_peserta($user);
        } elseif($query == 'check-job'){
            return $this->check_job();
        } elseif($query == 'toggle-ujian'){
            return $this->toggle_ujian($request);
        } elseif($query == 'rilis-token'){
            return $this->rilis_token($request);
        } else {
            echo $query;
        }
    }
    public function daftar_peserta($user){
        return view('proktor.daftar_peserta', compact('user'));
    }
    public function daftar_ptk($user){
        return view('proktor.daftar_ptk', compact('user'));
    }
    public function status_test($user){
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $all_ujian = Ujian::whereHas('event', function($query) use ($event){
            $query->where('id', $event->id);
        })->with(['mata_pelajaran', 'event'])->get();
        $rombongan_belajar = Rombongan_belajar::get();
        $aktif_token = Setting::where('key', 'token')->first();
        $token = ($aktif_token) ? '<strong>'.$aktif_token->value.' - Updated : '.date('H:i:s', strtotime($aktif_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>' : '';
        return view('proktor.status_test', compact('user', 'rombongan_belajar', 'token', 'all_ujian'));
    }
    public function status_test_old($user){
        $status = [
            'selesai' => User_exam::where(function($query){
                $query->where('exam_id', config('global.exam_id'));
                $query->where('status_ujian', 0);
            })->count(),
            'sedang' => User_exam::where(function($query){
                $query->where('exam_id', config('global.exam_id'));
                $query->where('status_ujian', 1);
            })->count()
        ];
        $ujian = Exam::find(config('global.exam_id'));
        /*if(!$status['sedang'] && $status['selesai']){
            $ujian = NULL;
            $exam->token = NULL;
            $exam->save();
        } else {
            $ujian = $exam;
        }*/
        $mata_ujian = ($ujian) ? Exam::where('pembelajaran_id', $ujian->pembelajaran_id)->get() : [];
        $pembelajaran = Pembelajaran::with('rombongan_belajar')->get();
        return view('proktor.status_test', compact('user', 'status', 'ujian', 'mata_ujian', 'pembelajaran'));
    }
    public function status_peserta($user){
        $all_sekolah = Sekolah::get();
        return view('proktor.status_peserta', compact('user', 'all_sekolah'));
    }
    public function reset_login_peserta($user){
        return view('proktor.reset_login', compact('user'));
    }
    public function hitung_server($user){
        /*
        $output = [
            'query' => $query,
            'jumlah' => $count.'/'.$jumlah,
            'percent' => $persentasi=round($count/$jumlah * 100,2),
        ];
        return response()->json($output);
        */
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $sekolah_id = [];
        if($event){
            foreach($event->peserta as $peserta){
                $sekolah_id[] = $peserta->sekolah->sekolah_id;
            }
        }
        $host_server = config('global.url_server').'status-download-event';
        $arguments = [
            'event_id' => $event->id,
        ];
        $client = new Client(); //GuzzleHttp\Client
        $curl = $client->post($host_server, [	
            'form_params' => $arguments
        ]);
        if($curl->getStatusCode() == 200){
            $output = [
                'server' => json_decode($curl->getBody()),
                'local' => [
                    'ptk' => Ptk::whereIn('sekolah_id', $sekolah_id)->count(),
                    'rombongan_belajar' => Rombongan_belajar::whereIn('sekolah_id', $sekolah_id)->count(),
                    'ujian' => Ujian::where('event_id', $event->id)->count(),
                    'anggota_rombel' => Anggota_rombel::whereHas('rombongan_belajar', function($query) use ($sekolah_id){
                        $query->whereIn('sekolah_id', $sekolah_id);
                    })->count(),
                    'exams' => Exam::whereHas('event', function($query) use ($event){
                        $query->where('events.id', $event->id);
                        $query->whereNull('sinkron');
                    })->count(),
                    'questions' => Question::whereHas('exam', function($query) use ($event){
                        $query->whereHas('event', function($query) use ($event){
                            $query->where('events.id', $event->id);
                            $query->whereNull('sinkron');
                        });
                    })->count(),
                    'answers' => Answer::whereHas('question', function($query) use ($event){
                        $query->whereHas('exam', function($query) use ($event){
                            $query->whereHas('event', function($query) use ($event){
                                $query->where('events.id', $event->id);
                                $query->whereNull('sinkron');
                            });
                        });
                    })->count(),
                ]
            ];
        } else {
            $output = [
                'response' => NULL,
                'success' => FALSE,
                'message' => 'Progress download gagal. Server tidak merespon. Silahkan refresh halaman ini!',
                'next' => NULL
            ];
        }
        return response()->json($output);
    }
    public function simpan(Request $request){
        $query = $request->route('query');
        if($query == 'sync'){
            Artisan::call('proses:sync', ['query' => 'download', 'data' => $request->all()]);
        } elseif($query == 'reset-login'){
            $insert = 0;
            $users_id = $request->users_id;
            foreach($users_id as $user_id){
                $user = User::find($user_id);
                $user->logout = TRUE;
                if($user->save()){
                    $insert++;
                }
            }
            if($insert){
                $output = [
                    'icon' => 'success',
                    'message' => 'Reset login berhasil!',
                ];
            } else {
                $output = [
                    'icon' => 'error',
                    'message' => 'Reset login gagal. Silahkan coba lagi!',
                ];
            }
            return response()->json($output);
        } elseif($query == 'reset-hasil'){
            $delete = User_exam::whereIn('user_exam_id', $request->user_exam_id)->delete();
            if($delete){
                $output = [
                    'icon' => 'success',
                    'message' => 'Reset ujian berhasil!',
                ];
            } else {
                $output = [
                    'icon' => 'error',
                    'message' => 'Reset ujian gagal. Silahkan coba lagi!',
                ];
            }
            return response()->json($output);
        } elseif($query == 'upload-hasil'){
            /*dd($request->all());
            $data = [
                'anggota_rombel_id' => $request->anggota_rombel_id,
                'ptk_id' => $request->ptk_id,
                'user_exam_id' => $request->user_exam_id,
                'exam_id' => $request->exam_id
            ];*/
            $data = User_exam::with('user_question')->find($request->user_exam_id);
            Artisan::call('proses:sync', ['query' => 'upload', 'data' => $data]);
        } elseif($query == 'ujian'){
            $exam = Exam::find($request->exam_id);
            $exam->aktif = 1;
            $exam->token = config('global.token');
            if($exam->save()){
                $output = [
                    'icon' => 'success',
                    'success' => TRUE,
                    'status' => 'Sukses menambah Mata Ujian Aktif',
                ];
            } else {
                $output = [
                    'icon' => 'danger',
                    'success' => FALSE,
                    'status' => 'Gagal menambah Mata Ujian Aktif',
                ];
            }
            return response()->json($output);
        }
    }
    public function toggle_ujian($request){
        $exam = Exam::find($request->exam_id);
        $exam->aktif = 0;
        $exam->token = NULL;
        if($exam->save()){
            $output = [
                'icon' => 'success',
                'success' => TRUE,
                'status' => 'Sukses menonaktifkan Mata Ujian',
            ];
        } else {
            $output = [
                'icon' => 'danger',
                'success' => FALSE,
                'status' => 'Gagal menonaktifkan Mata Ujian',
            ];
        }
        $count = Exam::whereAktif(1)->count();
        if(!$count){
            Setting::where('key', 'token')->delete();
        }
        $output['count'] = $count;
        return response()->json($output);
    }
    public function check_job(){
        if(config('global.opsi_token') == 'dinamis'){
            if(config('global.token')){
                $job = DB::table('jobs')->first();
                if($job){
                    $payload = json_decode($job->payload,true);
                    $available_at = $job->available_at;
                    $now = strtotime(date('H:i:s', strtotime(Carbon::now())));
                    if($available_at <= $now){
                        Artisan::call('queue:work --once');
                        $aktif_token = Setting::where('key', 'token')->first();
                        echo '<strong>'.$aktif_token->value.' - Updated : '.date('H:i:s', strtotime($aktif_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>';
                    } else {
                        $aktif_token = Setting::where('key', 'token')->first();
                        echo '<strong>'.$aktif_token->value.' - Updated : '.date('H:i:s', strtotime($aktif_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>';
                    }
                } else {
                    $aktif_token = Setting::where('key', 'token')->first();
                    TokenJob::dispatch()->delay($aktif_token->updated_at->addMinutes($this->menit));
                    echo '<strong>'.$aktif_token->value.' - Updated : '.date('H:i:s', strtotime($aktif_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>';
                }
            }
        } else {
            $aktif_token = Setting::where('key', 'token')->first();
            echo ($aktif_token) ? 'Token : <strong>'.$aktif_token->value.'</strong>' : '';
        }
    }
    public function rilis_token($request){
        $opsi = $request->opsi;
        Setting::updateOrCreate(
            [
                'key' => 'opsi_token'
            ],
            [
                'value' => $opsi
            ]
        );
        $token = Random::alphaUppercaseHumanString(6);
        $exams = Exam::whereAktif(1)->update(['token' => $token]);
        /*Setting::updateOrCreate(
            [
                'key' => 'token'
            ],
            [
                'value' => $token
            ]
        );*/
        $generatedToken = NULL;
        if($exams){
            $aktif_token = Setting::where('key', 'token')->first();
            if($aktif_token){
                if($opsi == 'dinamis'){
                    if ($aktif_token->updated_at->diffInMinutes(Carbon::now()) >= $this->menit) {
                        $aktif_token->value = $token;
                        //$exam->save();
                        TokenJob::dispatch()->delay(now()->addMinutes($this->menit));
                    }
                    $generatedToken = '<strong>'.$aktif_token->value.' - Updated : '.date('H:i:s', strtotime($aktif_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>';
                } else {
                    $generatedToken = 'Token : <strong>'.$aktif_token->value.'</strong>';
                }
            } else {
                $new_token = Setting::updateOrCreate(
                    [
                        'key' => 'token'
                    ],
                    [
                        'value' => $token
                    ]
                );
                TokenJob::dispatch()->delay(now()->addMinutes($this->menit));
                if($opsi == 'dinamis'){
                    $generatedToken = '<strong>'.$new_token->value.' - Updated : '.date('H:i:s', strtotime($new_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>';
                } else {
                    $generatedToken = 'Token : <strong>'.$new_token->value.'</strong>';
                }
            }
            $output = [
                'token' => $generatedToken,
                'icon' => 'success',
                'success' => TRUE,
                'status' => 'Token berhasil dirilis',
            ];
        } else {
            $output = [
                'token' => $generatedToken,
                'icon' => 'error',
                'success' => FALSE,
                'status' => 'Ujian aktif belum ada, token tidak dirilis',
            ];
        }
        return response()->json($output);
    }
    public function force_selesai(Request $request){
        $user_exam = User_exam::find($request->route('id'));
        $user_exam->status_ujian = 0;
        if($user_exam->save()){
            $output = [
                'icon' => 'success',
                'success' => TRUE,
                'status' => 'Force Selesai Sukses',
            ];
        } else {
            $output = [
                'icon' => 'error',
                'success' => FALSE,
                'status' => 'Force Selesai Gagal. Silahkan coba lagi!',
            ];
        }
        return response()->json($output);
    }
    public function hitung_data($query, $jumlah){
        //$event = Event::with('peserta.sekolah')->first();
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $sekolah_id = [];
        if($event){
            foreach($event->peserta as $peserta){
                $sekolah_id[] = $peserta->sekolah->sekolah_id;
            }
        }
        if($query == 'ptk'){
            $count = Ptk::whereIn('sekolah_id', $sekolah_id)->count();
        }elseif($query == 'rombongan_belajar'){
            $count = Rombongan_belajar::whereIn('sekolah_id', $sekolah_id)->count();
        }elseif($query == 'ujian'){
            $count = Ujian::where('event_id', $event->id)->count();
        } elseif($query == 'anggota_rombel'){
            $count = Anggota_rombel::whereIn('sekolah_id', $sekolah_id)->count();
        } elseif($query == 'exams'){
            $count = Exam::where(function($query) use ($event){
                $query->whereHas('event', function($query) use ($event){
                    $query->where('events.id', $event->id);
                });
                $query->whereNull('sinkron');
            })->count();
        } elseif($query == 'questions'){
            $count = Question::whereHas('exam', function($query) use ($event){
                $query->whereHas('event', function($query) use ($event){
                    $query->where('events.id', $event->id);
                });
                $query->whereNull('sinkron');
            })->count();
        } elseif($query == 'answers'){
            $count = Answer::whereHas('question', function($query) use ($event){
                $query->whereHas('exam', function($query) use ($event){
                    $query->whereHas('event', function($query) use ($event){
                        $query->where('events.id', $event->id);
                    });
                    $query->whereNull('sinkron');
                });
            })->count();
        }
        $output = [
            'query' => $query,
            'jumlah' => $count.'/'.$jumlah,
            'percent' => ($count) ? $persentasi=round($count/$jumlah * 100,2) : 0,
        ];
        return response()->json($output);
    }
    public function status_download($user){
        //$event = Event::with('peserta.sekolah')->first();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $sekolah_id = [];
        if($event){
            foreach($event->peserta as $peserta){
                $sekolah_id[] = $peserta->sekolah->sekolah_id;
            }
        }
        $sinkron = [
            'ptk' => Ptk::whereIn('sekolah_id', $sekolah_id)->count(),
            'rombongan_belajar' => Rombongan_belajar::whereIn('sekolah_id', $sekolah_id)->count(),
            'ujian' => Ujian::where('event_id', $event->id)->count(),
            'anggota_rombel' => Anggota_rombel::whereHas('rombongan_belajar', function($query) use ($sekolah_id){
                $query->whereIn('sekolah_id', $sekolah_id);
            })->count(),
            'exams' => Exam::whereHas('event', function($query) use ($event){
                $query->where('event_id', $event->id);
                $query->whereNull('sinkron');
            })->count(),
            'questions' => Question::whereHas('exam', function($query) use ($event){
                $query->whereHas('event', function($query) use ($event){
                    $query->where('event_id', $event->id);
                    $query->whereNull('sinkron');
                });
            })->count(),
            'answers' => Answer::whereHas('question', function($query) use ($event){
                $query->whereHas('exam', function($query) use ($event){
                    $query->whereHas('event', function($query) use ($event){
                        $query->where('event_id', $event->id);
                        $query->whereNull('sinkron');
                    });
                });
            })->count(),
        ];
        return view('proktor.status_download', compact('user', 'sinkron'));
    }
    public function get_status_download($user){
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $sekolah_id = [];
        if($event){
            foreach($event->peserta as $peserta){
                $sekolah_id[] = $peserta->sekolah->sekolah_id;
            }
        }
        $host_server = config('global.url_server').'status-download-event';
        $arguments = [
            'event_id' => $event->id,
        ];
        $client = new Client(); //GuzzleHttp\Client
        $curl = $client->post($host_server, [	
            'form_params' => $arguments
        ]);
        if($curl->getStatusCode() == 200){
            $response = json_decode($curl->getBody());
            $sinkron = [
                'success' => $response->success,
                'message' => $response->message,
                'server' => (array) $response->data,
                'local' => [
                    'ptk' => Ptk::whereIn('sekolah_id', $sekolah_id)->count(),
                    'rombongan_belajar' => Rombongan_belajar::whereIn('sekolah_id', $sekolah_id)->count(),
                    'ujian' => Ujian::where('event_id', $event->id)->count(),
                    'anggota_rombel' => Anggota_rombel::whereHas('rombongan_belajar', function($query) use ($sekolah_id){
                        $query->whereIn('sekolah_id', $sekolah_id);
                    })->count(),
                    'exams' => Exam::whereHas('event', function($query) use ($event){
                        $query->where('event_id', $event->id);
                        $query->whereNull('sinkron');
                    })->count(),
                    'questions' => Question::whereHas('exam', function($query) use ($event){
                        $query->whereHas('event', function($query) use ($event){
                            $query->where('event_id', $event->id);
                            $query->whereNull('sinkron');
                        });
                    })->count(),
                    'answers' => Answer::whereHas('question', function($query) use ($event){
                        $query->whereHas('exam', function($query) use ($event){
                            $query->whereHas('event', function($query) use ($event){
                                $query->where('event_id', $event->id);
                                $query->whereNull('sinkron');
                            });
                        });
                    })->count(),
                ]
            ];
        } else {
            $sinkron = [
                'success' => FALSE,
                'message' => 'Server tidak merespon',
                'server' => NULL,
                'local' => NULL
            ];
        }
        return view('proktor.get_status_download', compact('sinkron'));
    }
    public function proses_download(Request $request){
        //$event = Event::first();
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $host_server = config('global.url_server').'proses-download-event';
        $arguments = [
            'data' => $request->route('query'),
            'event_id' => $event->id,
        ];
        $client = new Client(); //GuzzleHttp\Client
        $curl = $client->post($host_server, [	
            'form_params' => $arguments
        ]);
        if($curl->getStatusCode() == 200){
            $output = json_decode($curl->getBody());
        } else {
            $output = [
                'response' => NULL,
                'success' => FALSE,
                'message' => 'Progress download gagal. Server tidak merespon. Silahkan refresh halaman ini!',
                'next' => NULL
            ];
        }
        return response()->json($output);
    }
    public function reset_login(Request $request){
        $user_id = $request->user_id;
        $user = User::find($user_id);
        $user->logout = TRUE;
        if($user->save()){
            $output = [
                'icon' => 'success',
                'message' => 'Reset login berhasil!',
            ];
        } else {
            $output = [
                'icon' => 'error',
                'message' => 'Reset login gagal. Silahkan coba lagi!',
            ];
        }
        return response()->json($output);
    }
}
