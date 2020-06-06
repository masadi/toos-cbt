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
use App\User;
use App\Event;
use App\Ujian;
use App\User_question;
use App\Jadwal;
use App\History;
use Delight\Random\Random;
use Codedge\Updater\UpdaterManager;
use Carbon\Carbon;
use App\Jobs\TokenJob;
use Artisan;
use Validator;
use Str;
use File;
use ZipArchive;
use Helper;
use PDF;
/*
output(): Outputs the PDF as a string.
save($filename): Save the PDF to a file
download($filename): Make the PDF downloadable by the user.
stream($filename): Return a response with the PDF to show in the browser.
*/
//use App\Mail\KirimAkun;
use Illuminate\Support\Facades\Mail;
use App\Notifications\KirimAkun;
class ProktorController extends Controller
{
    public function __construct()
    {
        $this->menit = 15;
    }
    public function kirim_wa(Request $request){
        $users = User::whereNotNull('phone_number')->get();
        //$request->user()->notify(new KirimAkun($user));
        foreach($users as $user){
            $user->notify(new KirimAkun($user));
        }
        return redirect()->route('home')->with('login-success', 'WA berhasil dikirim');
        $account_sid = config('services.twilio.sid');
        $auth_token = config('services.twilio.token');
        // In production, these should be environment variables. E.g.:
        // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

        // A Twilio number you own with SMS capabilities
        $twilio_number = config('services.twilio.whatsapp_from');

        $client = new Twilio($account_sid, $auth_token);
        $client->messages->create(
            // Where to send a text message (your cell phone?)
            '+6285232298529',
            array(
                'from' => $twilio_number,
                'body' => 'I sent this message in under 10 minutes!'
            )
        );
    }
    public function cetak_kartu($id) 
	{
        //return view('proktor.document');
		$all_anggota = Anggota_rombel::with(['rombongan_belajar.jadwal', 'rombongan_belajar.jurusan_sp', 'peserta_didik.user'])->where('rombongan_belajar_id', $id)->get();
        $pdf = PDF::loadView('proktor.blank');
        foreach($all_anggota as $anggota){
            $rapor_cover = view('proktor.document', compact('anggota'));
            $pdf->getMpdf()->WriteHTML($rapor_cover);
            $pdf->getMpdf()->AddPage('L');
        }
        $pdfFilePath = 'document.pdf';
        /*
		$pdf = PDF::loadView('proktor.document', $data, [], [
            'title' => 'Another Title',
            'margin_top' => 0,
            'format' => [190, 236]
        ]);//->save($pdfFilePath);*/
		return $pdf->stream($pdfFilePath);
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
        } elseif($query == 'proses-sync'){
            return $this->proses_sync($request);
        } elseif($query == 'jadwal-ujian'){
            return $this->jadwal_ujian($user);
        } elseif($query == 'tambah-jadwal'){
            return $this->tambah_jadwal($request);
        } elseif($query == 'test'){
            return $this->test($request);
        } elseif($query == 'hapus-data'){
            return $this->hapus_data($request);
        } else {
            echo $query;
        }
    }
    public function jadwal_ujian($user){
        $event = Event::where('kode', $user->username)->first();
        $all_tingkat = Rombongan_belajar::select('tingkat')->groupBy('tingkat')->orderBy('tingkat')->where(function($query) use ($event, $user){
            if($event){
                $query->whereIn('sekolah_id', function($query) use ($event){
                    $query->select('sekolah_id')->from('peserta_events')->where('event_id', $event->id);
                });
            } else {
                $query->where('sekolah_id', $user->sekolah_id);
            }
        })->get();
        return view('proktor.jadwal_ujian', compact('user', 'all_tingkat'));
    }
    public function tambah_jadwal($request){
        $rombongan_belajar = Rombongan_belajar::with('pembelajaran')->find($request->rombongan_belajar_id);
        return view('proktor.tambah_jadwal', compact('rombongan_belajar'));
    }
    public function proses_sync($request){
        $user = auth()->user();
        $sync_file = public_path('storage/uploads/'.$request->sync_file);
        //$data = json_decode(Helper::prepare_receive($sync_file));
        $sync_file = File::get($sync_file);
        $data = json_decode(Helper::prepare_receive($sync_file));
        Artisan::call('proses:sync', ['query' => 'proses-sync', 'data' => $data, 'timezone' => $user->timezone]);
        //unlink(storage_path('app/public/uploads/'.$request->sync_file));
        File::delete($sync_file);
        $output = [
            'title' => 'Berhasil',
            'text' => 'Proses sync offline selesai',
            'icon' => 'success'
        ];
        return response()->json($output);
        //public_path('storage/uploads/'.$request->zip_file);
    }
    public function test($request){
        $user = [
            'id' => '0007086646',
            'exam_id' => ['a60ccd00-45bd-49e1-9d14-3056eb24fcce'],
        ];
        $user1 = [
            'id' => '0024820748',
            'exam_id' => ['defdc06c-743c-4b1b-b6eb-4112039b87b9'],
        ];
        $user1 = [
            'id' => '0020412825',
            'exam_id' => ['d1cdfb7a-faaf-4794-b2c4-bf7cc2984d57'],
        ];
        $user1 = [
            'id' => '0025980440',
            'exam_id' => ['68781a75-696f-4ab8-aec6-7376de9427a5'],
        ];
        $find = User::where('username', $user['id'])->first();
        $a = User_exam::with('user_question')->where(function($query) use ($user, $find){
            $query->whereIn('exam_id', $user['exam_id']);
            $query->where('user_id', $find->user_id);
        })->get();
        foreach($a as $b){
            $b->delete();
        }
        //$a->delete();
        dump($a);
    }
    public function daftar_peserta($user){
        $event = Event::where('kode', $user->username)->first();
        $all_tingkat = Rombongan_belajar::select('tingkat')->groupBy('tingkat')->orderBy('tingkat')->where(function($query) use ($event, $user){
            if($event){
                $query->whereIn('sekolah_id', function($query) use ($event){
                    $query->select('sekolah_id')->from('peserta_events')->where('event_id', $event->id);
                });
            } else {
                $query->where('sekolah_id', $user->sekolah_id);
            }
        })->get();
        return view('proktor.daftar_peserta', compact('user', 'all_tingkat'));
    }
    public function daftar_ptk($user){
        return view('proktor.daftar_ptk', compact('user'));
    }
    public function status_test($user){
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        if($event){
            $all_ujian = Ujian::whereHas('event', function($query) use ($event){
                $query->where('id', $event->id);
            })->with(['mata_pelajaran', 'event'])->get();
            $aktif_token = Exam::whereAktif(1)->whereHas('event', function($query) use ($event){
                $query->where('event_id', $event->id);
            })->first();
        } else {
            $all_ujian = '';
            /*
            $all_ujian = Exam::whereHas('pembelajaran', function($query) use ($user){
                $query->where('sekolah_id', $user->sekolah_id);
            })->get();
            */
            $aktif_token = Exam::whereAktif(1)->whereHas('pembelajaran', function($query) use ($user){
                $query->where('sekolah_id', $user->sekolah_id);
            })->first();
            $rombongan_belajar = Rombongan_belajar::where('sekolah_id', $user->sekolah_id)->get();
        }
        $opsi_token = config('global.opsi_token');
        if($opsi_token == 'statis'){
            $token = ($aktif_token) ? '<strong>'.$aktif_token->token.'</strong>' : '';
        } else {
            $token = ($aktif_token) ? '<strong>'.$aktif_token->token.' - Updated : '.date('H:i:s', strtotime($aktif_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>' : '';
        }
        return view('proktor.status_test', compact('user', 'rombongan_belajar', 'token', 'all_ujian', 'event'));
    }
    public function status_peserta($user){
        $all_sekolah = Sekolah::get();
        return view('proktor.status_peserta', compact('user', 'all_sekolah'));
    }
    public function reset_login_peserta($user){
        return view('proktor.reset_login', compact('user'));
    }
    public function hitung_server($user){
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $sekolah_id = [];
        if($event){
            foreach($event->peserta as $peserta){
                $sekolah_id[] = $peserta->sekolah->sekolah_id;
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
        } else {
            $server = Server::where('id_server', $user->username)->first();
            $host_server = config('global.url_server').'status-download';
            $arguments = [
                'server_id' => $server->server_id,
            ];
            $client = new Client(); //GuzzleHttp\Client
            $curl = $client->post($host_server, [	
                'form_params' => $arguments
            ]);
            if($curl->getStatusCode() == 200){
                $output = [
                    'server' => json_decode($curl->getBody()),
                    'local' => [
                        'ptk' => Ptk::where('sekolah_id', $server->sekolah_id)->count(),
                        'rombongan_belajar' => Rombongan_belajar::where(function($query) use ($server){
                            $query->where('server_id', $server->server_id);
                        })->count(),
                        'pembelajaran' => Pembelajaran::whereHas('rombongan_belajar', function($query) use ($server){
                            $query->where('server_id', $server->server_id);
                        })->count(),
                        'anggota_rombel' => Anggota_rombel::whereHas('rombongan_belajar', function($query) use ($server){
                            $query->where('server_id', $server->server_id);
                        })->count(),
                        'exams' => Exam::whereHas('pembelajaran', function($query) use ($server){
                            $query->whereHas('rombongan_belajar', function($query) use ($server){
                                $query->where('server_id', $server->server_id);
                            });
                            $query->whereNull('sinkron');
                        })->count(),
                        'questions' => Question::whereHas('exam', function($query) use ($server){
                            $query->whereHas('pembelajaran', function($query) use ($server){
                                $query->whereHas('rombongan_belajar', function($query) use ($server){
                                    $query->where('server_id', $server->server_id);
                                });
                                $query->whereNull('sinkron');
                            });
                        })->count(),
                        'answers' => Answer::whereHas('question', function($query) use ($server){
                            $query->whereHas('exam', function($query) use ($server){
                                $query->whereHas('pembelajaran', function($query) use ($server){
                                    $query->whereHas('rombongan_belajar', function($query) use ($server){
                                        $query->where('server_id', $server->server_id);
                                    });
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
            $delete = User_exam::find($request->user_exam_id);
            $delete_success = 0;
            foreach($delete as $del){
                $user_folder = Helper::user_folder($del->user_id);
                $exam_folder = Helper::exam_folder($del->user_id, $del->exam_id);
                if (!File::isDirectory($user_folder)) {
                    //MAKA FOLDER TERSEBUT AKAN DIBUAT
                    File::makeDirectory($user_folder);
                }
                if (!File::isDirectory($exam_folder)) {
                    //MAKA FOLDER TERSEBUT AKAN DIBUAT
                    File::makeDirectory($exam_folder);
                }
                $get_ujian = Exam::withCount(['question', 'user_question' => function($query) use ($del){
                    $query->where('user_questions.user_id', $del->user_id);
                }])->with(['question' => function($query){
                    $query->with('answers');
                    $query->orderBy('soal_ke');
                }, 'user_exam' => function($query) use ($del){
                    $query->where('user_exams.user_id', $del->user_id);
                }])->find($del->exam_id);
                $collection = collect($get_ujian->question);
                $shuffled = $collection->shuffle();
                $questions = $shuffled->toArray();
                unset($get_ujian->question);
                $exam_json = [
                    'exam' => $get_ujian->toArray(),
                    'questions' => $questions,
                ];
                $gabung = collect($exam_json);
                //Storage::disk('public')->put($json_file_all, $shuffled->toJson());
                File::put($user_folder.'/exam.json', $gabung->toJson());
                $delete_success++;
                $del->delete();
            }
            if($delete_success){
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
            $user = auth()->user();
            /*dd($request->all());
            $data = [
                'anggota_rombel_id' => $request->anggota_rombel_id,
                'ptk_id' => $request->ptk_id,
                'user_exam_id' => $request->user_exam_id,
                'exam_id' => $request->exam_id
            ];*/
            $data = User_exam::with(['user','user_question'])->withCount('user_question')->find($request->user_exam_id);
            Artisan::call('proses:sync', ['query' => 'upload', 'data' => $data, 'timezone' => $user->timezone]);
        } elseif($query == 'ujian'){
            $messages = [
                'rombongan_belajar_id.required' => 'Rombongan Belajar tidak boleh kosong',
                'pembelajaran_id.required' => 'Mata Pelajaran tidak boleh kosong',
                'exam_id.required' => 'Mata Ujian tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'rombongan_belajar_id' => 'required',
                'pembelajaran_id' => 'required',
                'exam_id' => 'required',
             ],
            $messages
            )->validate();
            $exam = Exam::with(['pembelajaran', 'question' => function($query){
                //$query->with('answers');
                $query->orderBy('soal_ke');
            }])->find($request->exam_id);
            $exam_file = $exam->exam_id.'.json';
            Storage::disk('public')->put($exam_file, json_encode($exam->toArray()));
            $all_user = User::whereHas('peserta_didik', function($query) use ($exam){
                $query->whereHas('anggota_rombel', function($query) use ($exam){
                    $query->where('rombongan_belajar_id', $exam->pembelajaran->rombongan_belajar_id);
                });
            })->get();
            //$collection = collect($exam->question);
            //$keyed = $collection->keyBy('question_id');
            //$keyed->all();
            //$keys = $keyed->keys();
            //$collection = collect($keys);
            //DB::enableQueryLog();
            if($all_user->count()){
                foreach($all_user as $user){

                    $collection = collect($exam->question);
                    $shuffled = $collection->shuffle();
                    $questions = $shuffled->toArray();
                    unset($exam->question);
                    $exam_json = [
                        'exam' => $exam->toArray(),
                        'questions' => $questions,
                    ];
                    /*History::updateOrCreate(
                        [
                            'user_id' => $user->user_id,
                            'exam_id' => $exam->exam_id,
                        ],
                        [
                            'questions' => serialize($exam_json),
                        ]
                    );*/
                    $gabung = collect($exam_json);
                    $user_folder = Helper::user_folder($user->user_id);
                    //Storage::disk('public')->put($json_file_all, $shuffled->toJson());
                    File::put($user_folder.'/exam.json', $gabung->toJson());
                    /*
                    $exam_folder = Helper::exam_folder($user->user_id, $exam->exam_id);
                    if (!File::isDirectory($user_folder)) {
                        //MAKA FOLDER TERSEBUT AKAN DIBUAT
                        File::makeDirectory($user_folder);
                    }
                    if (!File::isDirectory($exam_folder)) {
                        //MAKA FOLDER TERSEBUT AKAN DIBUAT
                        File::makeDirectory($exam_folder);
                    }
                    $get_ujian = Exam::withCount(['question', 'user_question' => function($query) use ($user){
                        $query->where('user_questions.user_id', $user->user_id);
                    }])->with(['question' => function($query){
                        $query->with('answers');
                        $query->orderBy('soal_ke');
                    }, 'user_exam' => function($query) use ($user){
                        $query->where('user_exams.user_id', $user->user_id);
                    }])->find($exam->exam_id);
                    $collection = collect($get_ujian->question);
                    $shuffled = $collection->shuffle();
                    $questions = $shuffled->toArray();
                    unset($get_ujian->question);
                    $exam_json = [
                        'exam' => $get_ujian->toArray(),
                        'questions' => $questions,
                    ];
                    $gabung = collect($exam_json);
                    //Storage::disk('public')->put($json_file_all, $shuffled->toJson());
                    File::put($user_folder.'/exam.json', $gabung->toJson());
                    */
                }
            }
            if($exam){
                $exam->aktif = 1;
                $exam->token = config('global.token');
                if($exam->save()){
                    $output = [
                        'icon' => 'success',
                        'title' => 'Berhasil',
                        'status' => 'Sukses menambah Mata Ujian Aktif',
                    ];
                } else {
                    $output = [
                        'icon' => 'error',
                        'title' => 'Gagal',
                        'status' => 'Gagal menambah Mata Ujian Aktif',
                    ];
                }
            } else {
                $output = [
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'status' => 'Mata Ujian tidak boleh kosong',
                ];
            }
            return response()->json($output);
        } elseif($query == 'upload-sync'){
            $validator = Validator::make($request->all(), [
                'files.*' => 'required|mimes:zip'
            ]);
            if ($validator->passes()) {
                if ($request->hasFile('files')) {
                    $files = $request->file('files');
                    foreach($files as $file){
                        $file->storeAs('public/uploads', $file->getClientOriginalName());//MAKA SIMPAN FILE TERSEBUT DI STORAGE/APP/PUBLIC/UPLOADS
                        $zip = new ZipArchive;
                        if (!File::isDirectory(storage_path('app/public/uploads'))) {
                            //MAKA FOLDER TERSEBUT AKAN DIBUAT
                            File::makeDirectory(storage_path('app/public/uploads'));
                        }
                        $sync_file = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        if ($zip->open(storage_path('app/public/uploads/'.$file->getClientOriginalName())) === TRUE) {
                            $zip->extractTo(public_path('storage/uploads/'));
                            $zip->close();
                            $output = [
                                'icon' => 'success',
                                'success' => TRUE,
                                'title' => 'Berhasil',
                                'text' => 'Unggah Data Sync Berhasil',
                                'sync_file' => $sync_file.'.sync',
                            ];
                        } else {
                            $output = [
                                'icon' => 'error',
                                'success' => FALSE,
                                'title' => 'Gagal',
                                'text' => 'File ZIP gagal di ekstrak. Silahkan unggah ulang!!!',
                                'sync_file' => NULL,
                            ];
                        }
                        File::delete(storage_path('app/public/uploads/'.$file->getClientOriginalName()));
                    }
                } else {
                    $output = [
                        'icon' => 'error',
                        'success' => FALSE,
                        'title' => 'Gagal',
                        'text' => 'File ZIP gagal di unggah!',
                        'sync_file' => NULL,
                    ];
                }
            } else {
                $output = [
                    'icon' => 'error',
                    'success' => FALSE,
                    'title' => 'Gagal',
                    'text' => 'File harus ekstensi ZIP',
                    'sync_file' => NULL,
                ];
            }
            return response()->json($output);
        } elseif($query == 'jadwal-ujian'){
            $messages = [
                'rombongan_belajar_id.required' => 'Rombongan Belajar tidak boleh kosong',
                'pembelajaran_id.required' => 'Mata Pelajaran tidak boleh kosong',
                'from.required' => 'Jam Mulai tidak boleh kosong',
                'to.required' => 'Jam Berakhir tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'rombongan_belajar_id' => 'required',
                'pembelajaran_id' => 'required',
                'from' => 'required',
                'to' => 'required',
             ],
            $messages
            )->validate();
            Jadwal::updateOrCreate(
                [
                    'pembelajaran_id' => $request->pembelajaran_id,
                ],
                [
                    'tanggal' => $request->date,
                    'rombongan_belajar_id' => $request->rombongan_belajar_id,
                    'from' => $request->from,
                    'to' => $request->to,
                ]
            );
            $output = [
                'icon' => 'success',
                'text' => 'Jadwal berhasil disimpan',
                'title' => 'Berhasil',
            ];
            return response()->json($output);
        } elseif($query == 'update-jadwal-ujian'){
            $messages = [
                'rombongan_belajar_id.required' => 'Rombongan Belajar tidak boleh kosong',
                'pembelajaran_id.required' => 'Mata Pelajaran tidak boleh kosong',
                'from.required' => 'Jam Mulai tidak boleh kosong',
                'to.required' => 'Jam Berakhir tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'rombongan_belajar_id' => 'required',
                'pembelajaran_id' => 'required',
                'from' => 'required',
                'to' => 'required',
             ],
            $messages
            )->validate();
            $jadwal = Jadwal::find($request->jadwal_id);
            $jadwal->pembelajaran_id = $request->pembelajaran_id;
            $jadwal->tanggal = $request->date;
            $jadwal->rombongan_belajar_id = $request->rombongan_belajar_id;
            $jadwal->from = $request->from;
            $jadwal->to = $request->to;
            if($jadwal->save()){
                $output = [
                    'icon' => 'success',
                    'text' => 'Jadwal berhasil diperbaharui',
                    'title' => 'Berhasil',
                ];
            } else {
                $output = [
                    'icon' => 'error',
                    'text' => 'Jadwal gagal diperbaharui',
                    'title' => 'Gagal',
                ];
            }
            return response()->json($output);
        } elseif($query == 'kirim-akun'){
            $all_anggota = Anggota_rombel::where('rombongan_belajar_id', $request->rombongan_belajar_id)->get();
            foreach($all_anggota as $anggota){
                //Mail::to($anggota->peserta_didik->user->email)->send(new KirimAkun($anggota));
                if($anggota->peserta_didik->user->phone_number){
                    $anggota->peserta_didik->user->notify(new KirimAkun($anggota->peserta_didik->user));
                }
            }
            $output = [
                'icon' => 'success',
                'title' => 'Berhasil',
                'text' => 'Kirim akses pengguna berhasil',
            ];
            return response()->json($output);
        } else {
            echo 'simpan '.$query.' belum tersedia';
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
            Setting::where('key', 'opsi_token')->delete();
        }
        $output['count'] = $count;
        return response()->json($output);
    }
    public function check_job(){
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        if($event){
            $aktif_token = Exam::whereAktif(1)->whereHas('event', function($query) use ($event){
                $query->where('event_id', $event->id);
            })->first();
        } else {
            $aktif_token = Exam::whereAktif(1)->whereHas('pembelajaran', function($query) use ($user){
                $query->where('sekolah_id', $user->sekolah_id);
            })->first();
        }
        if(config('global.opsi_token') == 'dinamis'){
            if(config('global.token')){
                $job = DB::table('jobs')->first();
                if($job){
                    $payload = json_decode($job->payload,true);
                    $available_at = $job->available_at;
                    $now = strtotime(date('H:i:s', strtotime(Carbon::now($user->timezone))));
                    if($available_at <= $now){
                        Artisan::call('queue:work --once');
                        //$aktif_token = Setting::where('key', 'token')->first();
                        echo ($aktif_token) ? '<strong>'.$aktif_token->token.' - Updated : '.date('H:i:s', strtotime($aktif_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>' : '';
                    } else {
                        //$aktif_token = Setting::where('key', 'token')->first();
                        echo ($aktif_token) ? '<strong>'.$aktif_token->token.' - Updated : '.date('H:i:s', strtotime($aktif_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>' : '';
                    }
                } else {
                    //$aktif_token = Setting::where('key', 'token')->first();
                    if($aktif_token){
                        TokenJob::dispatch()->delay($aktif_token->updated_at->addMinutes($this->menit));
                        echo '<strong>'.$aktif_token->token.' - Updated : '.date('H:i:s', strtotime($aktif_token->updated_at)). ' - Interval '.$this->menit.' Menit</strong>';
                    }
                }
            }
        } else {
            //$aktif_token = Setting::where('key', 'token')->first();
            echo ($aktif_token) ? 'Token : <strong>'.$aktif_token->token.'</strong>' : '';
        }
    }
    public function rilis_token($request){
        $user = auth()->user();
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
                    if ($aktif_token->updated_at->diffInMinutes(Carbon::now($user->timezone)) >= $this->menit) {
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
                TokenJob::dispatch()->delay(now($user->timezone)->addMinutes($this->menit));
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
        /*
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
        return response()->json($output);*/
        $insert = 0;
        $ujian_id = $request->ujian_id;
        $question_id = $request->question_id;
        $answer_id = $request->answer_id;
        $user_exam = User_exam::find($request->route('id'));
        $user_exam->status_upload = 0;
        $user_exam->status_ujian = 0;
        $user_exam->force_selesai = 1;
        if($user_exam->save()){
            $exam_folder = Helper::exam_folder($user_exam->user_id, $ujian_id);
            $all_files = File::allfiles($exam_folder);
            foreach($all_files as $file){
                File::delete($file);
                $insert++;
            }
        }
        if($insert){
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
        $count = 0;
        if($event){
            foreach($event->peserta as $peserta){
                $sekolah_id[] = $peserta->sekolah->sekolah_id;
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
        } else {
            $server = Server::where('id_server', $user->username)->first();
            if($query == 'ptk'){
                $count = Ptk::where('sekolah_id', $server->sekolah_id)->count();
            }elseif($query == 'rombongan_belajar'){
                $count = Rombongan_belajar::where(function($query) use ($server){
                    $query->where('server_id', $server->server_id);
                })->count();
            }elseif($query == 'pembelajaran'){
                $count = Pembelajaran::whereHas('rombongan_belajar', function($query) use ($server){
                    $query->where('server_id', $server->server_id);
                })->count();
            } elseif($query == 'anggota_rombel'){
                $count = Anggota_rombel::whereHas('rombongan_belajar', function($query) use ($server){
                    $query->where('server_id', $server->server_id);
                })->count();
            } elseif($query == 'exams'){
                $count = Exam::whereHas('pembelajaran', function($query) use ($server){
                    $query->whereHas('rombongan_belajar', function($query) use ($server){
                        $query->where('server_id', $server->server_id);
                    });
                    $query->whereNull('sinkron');
                })->count();
            } elseif($query == 'questions'){
                $count = Question::whereHas('exam', function($query) use ($server){
                    $query->whereHas('pembelajaran', function($query) use ($server){
                        $query->whereHas('rombongan_belajar', function($query) use ($server){
                            $query->where('server_id', $server->server_id);
                        });
                        $query->whereNull('sinkron');
                    });
                })->count();
            } elseif($query == 'answers'){
                $count = Answer::whereHas('question', function($query) use ($server){
                    $query->whereHas('exam', function($query) use ($server){
                        $query->whereHas('pembelajaran', function($query) use ($server){
                            $query->whereHas('rombongan_belajar', function($query) use ($server){
                                $query->where('server_id', $server->server_id);
                            });
                            $query->whereNull('sinkron');
                        });
                    });
                })->count();
            }
        }
        $output = [
            'query' => $query,
            'jumlah' => $count.'/'.$jumlah,
            'percent' => ($count) ? round($count/$jumlah * 100,2) : 0,
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
        } else {
            $server = Server::where('id_server', $user->username)->first();
            $sinkron = [
                'ptk' => Ptk::where('sekolah_id', $server->sekolah_id)->count(),
                'rombongan_belajar' => Rombongan_belajar::where(function($query) use ($server){
                    $query->where('server_id', $server->server_id);
                })->count(),
                'pembelajaran' => Pembelajaran::whereHas('rombongan_belajar', function($query) use ($server){
                    $query->where('server_id', $server->server_id);
                })->count(),
                'anggota_rombel' => Anggota_rombel::whereHas('rombongan_belajar', function($query) use ($server){
                    $query->where('server_id', $server->server_id);
                })->count(),
                'exams' => Exam::whereHas('pembelajaran', function($query) use ($server){
                    $query->whereHas('rombongan_belajar', function($query) use ($server){
                        $query->where('server_id', $server->server_id);
                    });
                    $query->whereNull('sinkron');
                })->count(),
                'questions' => Question::whereHas('exam', function($query) use ($server){
                    $query->whereHas('pembelajaran', function($query) use ($server){
                        $query->whereHas('rombongan_belajar', function($query) use ($server){
                            $query->where('server_id', $server->server_id);
                        });
                        $query->whereNull('sinkron');
                    });
                })->count(),
                'answers' => Answer::whereHas('question', function($query) use ($server){
                    $query->whereHas('exam', function($query) use ($server){
                        $query->whereHas('pembelajaran', function($query) use ($server){
                            $query->whereHas('rombongan_belajar', function($query) use ($server){
                                $query->where('server_id', $server->server_id);
                            });
                            $query->whereNull('sinkron');
                        });
                    });
                })->count(),
            ];
        }
        return view('proktor.status_download', compact('user', 'sinkron'));
    }
    public function get_status_download($user){
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $sekolah_id = [];
        if($event){
            foreach($event->peserta as $peserta){
                $sekolah_id[] = $peserta->sekolah->sekolah_id;
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
        } else {
            $server = Server::where('id_server', $user->username)->first();
            $host_server = config('global.url_server').'status-download';
            $arguments = [
                'server_id' => $server->server_id,
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
                        'ptk' => Ptk::where('sekolah_id', $server->sekolah_id)->count(),
                        'rombongan_belajar' => Rombongan_belajar::where(function($query) use ($server){
                            $query->where('server_id', $server->server_id);
                        })->count(),
                        'pembelajaran' => Pembelajaran::whereHas('rombongan_belajar', function($query) use ($server){
                            $query->where('server_id', $server->server_id);
                        })->count(),
                        'anggota_rombel' => Anggota_rombel::whereHas('rombongan_belajar', function($query) use ($server){
                            $query->where('server_id', $server->server_id);
                        })->count(),
                        'exams' => Exam::whereHas('pembelajaran', function($query) use ($server){
                            $query->whereHas('rombongan_belajar', function($query) use ($server){
                                $query->where('server_id', $server->server_id);
                            });
                            $query->whereNull('sinkron');
                        })->count(),
                        'questions' => Question::whereHas('exam', function($query) use ($server){
                            $query->whereHas('pembelajaran', function($query) use ($server){
                                $query->whereHas('rombongan_belajar', function($query) use ($server){
                                    $query->where('server_id', $server->server_id);
                                });
                                $query->whereNull('sinkron');
                            });
                        })->count(),
                        'answers' => Answer::whereHas('question', function($query) use ($server){
                            $query->whereHas('exam', function($query) use ($server){
                                $query->whereHas('pembelajaran', function($query) use ($server){
                                    $query->whereHas('rombongan_belajar', function($query) use ($server){
                                        $query->where('server_id', $server->server_id);
                                    });
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
        }
        return view('proktor.get_status_download', compact('sinkron'));
    }
    public function proses_download(Request $request){
        //$event = Event::first();
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        if($event){
            $host_server = config('global.url_server').'proses-download-event';
            $arguments = [
                'data' => $request->route('query'),
                'offset' => $request->route('offset'),
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
        } else {
            $server = Server::where('id_server', $user->username)->first();
            $host_server = config('global.url_server').'proses-download';
            $arguments = [
                'data' => $request->route('query'),
                'offset' => $request->route('offset'),
                'server_id' => $server->server_id,
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
    public function hapus_data($request){
        return view('proktor.hapus_data');
    }
}
