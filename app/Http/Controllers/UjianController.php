<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use App\Ptk;
use App\Exam;
use App\Question;
use App\Bank_soal;
use App\Answer;
use App\User_exam;
use App\User_question;
use App\Event;
use File;
use Illuminate\Support\Facades\Storage;
use pcrov\JsonReader\JsonReader;
use Carbon\Carbon;
class UjianController extends Controller
{
    public function index(Request $request){
        $user = auth()->user();
        $now = Carbon::now($user->timezone)->toDateTimeString();
        $reader = new JsonReader();
        $ujian_id = $request->ujian_id;
        $json_file_ujian = 'ujian-'.$user->user_id.'-'.$ujian_id.'.json';
        if(Storage::disk('public')->exists($json_file_ujian)){
            //$ujian = Storage::disk('public')->get($json_file_ujian);
            //$ujian = json_decode($ujian);
            $reader->open('storage/'.$json_file_ujian);
            $ujian = '';
            if($reader->read()) {
                $collection = collect($reader->value());
                $ujian = $collection->toJson();
                $ujian = json_decode($ujian);
            }
        } else {
            $ujian = Exam::withCount(['question', 'user_question' => function($query) use ($user){
                $query->where('user_questions.user_id', $user->user_id);
            }])->with(['question' => function($query){
                $query->with('answers');
                $query->orderBy('soal_ke');
            }, 'user_exam' => function($query) use ($user){
                $query->where('user_exams.user_id', $user->user_id);
            }])->find($ujian_id);
            Storage::disk('public')->put($json_file_ujian, $ujian->toJson());
        }
        $jumlah_jawaban_siswa = $ujian->user_question_count;
        $waktu_ujian = time() + ($ujian->durasi * 60);
        if($ujian->question_count){
            $json_file_all = 'all-'.$user->user_id.'-'.$ujian_id.'.json';
            if(!Storage::disk('public')->exists($json_file_all)){
                $collection = collect($ujian->question);
                $shuffled = $collection->shuffle();
                $first = $shuffled->first();
                $all = $shuffled->all();
                Storage::disk('public')->put($json_file_all, $shuffled->toJson());
            } else {
                /*
                $all = Storage::disk('public')->get($json_file_all);
                $all = json_decode($all);
                $all = collect($all);
                $first = $all->first();
                $all = $all->all();*/
                $reader->open('storage/'.$json_file_all);
                if ($reader->read()) {
                    $collection = collect($reader->value());
                    $all = $collection->toJson();
                    $all = json_decode($all);
                    $all = collect($all);
                    $first = $all->first();
                    $all = $all->all();
                }
            }
            $questions = [$first];
            $current_id = $first->question_id;
            $page = 0;
            $user_exam = User_exam::updateOrCreate(
                [
                    'exam_id'   => $ujian_id,
                    'anggota_rombel_id' => ($user->peserta_didik) ? $user->peserta_didik->anggota_rombel->anggota_rombel_id : NULL,
                    'ptk_id' => $user->ptk_id,
                    'user_id' => $user->user_id,
                ],
                [
                    'status_ujian' => 1
                ]
            );
            /*if($this->jumlah_jawaban_siswa($user->user_id)){
                $json_file_utama = 'all-'.$user->user_id.'-'.$ujian_id.'.json';
                $all = Storage::disk('public')->get($json_file_utama);
                $all = json_decode($all);
                foreach($all as $s){
                    $keys[] = $s->question_id;
                }
                $questions = [$all[0]];
                $current_id = $all[0]->question_id;
            } else {
                $keys = [];
                $i=1;
                foreach($all as $s){
                    $keys[] = $s->question_id;
                    $i++;
                }
            }*/
            $i=1;
            foreach($all as $s){
                $keys[] = $s->question_id;
                $i++;
            }
            $json_file_jawaban = 'user_question-'.$user->user_id.'-'.$current_id.'.json';
            $jawaban_siswa = NULL;
            if(Storage::disk('public')->exists($json_file_jawaban)){
                //$jawaban_siswa = Storage::disk('public')->get($json_file_jawaban);
                $reader->open('storage/'.$json_file_jawaban);
                if ($reader->read()) {
                    $jawaban_siswa = collect($reader->value());
                    $jawaban_siswa = $jawaban_siswa->toJson();
                }
                $jawaban_siswa = json_decode($jawaban_siswa);
            }
            return view('ujian.proses_ujian', compact('ujian', 'questions', 'user_exam', 'user', 'jumlah_jawaban_siswa', 'jawaban_siswa', 'all', 'page', 'keys', 'current_id', 'reader', 'now'));
        } else {
            $ujian = '';
            return view('ujian.soal_tidak_lengkap', compact('ujian', 'now'));
        }
    }
    private function jumlah_jawaban_siswa($user_id){
        $path = public_path('storage');
        $all_files = File::allfiles($path);
        //$all_files = Storage::disk('public')->files();
        $all_files = collect($all_files)->filter(function ($item) use ($user_id) {
            // replace stristr with your choice of matching function
            return false !== stristr($item, 'user_question-'.$user_id);
        });
        return $all_files->count();
    }
    public function get_soal(Request $request){
        //dump($request->all());
        $reader = new JsonReader();
        $user = auth()->user();
        
        if(!$request->ujian_id){
            $output = [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Permintaan tidak sah'
            ];
            return response()->json($output);
        }
        
        $json_file_utama = 'all-'.$user->user_id.'-'.$request->ujian_id.'.json';
        //$all = Storage::disk('public')->get($json_file_utama);
        //$all = json_decode($all);
        if(Storage::disk('public')->exists($json_file_utama)){
            $reader->open('storage/'.$json_file_utama);
            if ($reader->read()) {
                $all = collect($reader->value());
                //$all = $all->toJson();
            }
        } else {
            $json_file_ujian = 'ujian-'.$user->user_id.'-'.$request->ujian_id.'.json';
            $json_file_all = 'all-'.$user->user_id.'-'.$request->ujian_id.'.json';
            $ujian = Exam::withCount(['question', 'user_question' => function($query) use ($user){
                $query->where('user_questions.user_id', $user->user_id);
            }])->with(['question' => function($query){
                $query->with('answers');
                $query->orderBy('soal_ke');
            }, 'user_exam' => function($query) use ($user){
                $query->where('user_exams.user_id', $user->user_id);
            }])->find($request->ujian_id);
            Storage::disk('public')->put($json_file_ujian, $ujian->toJson());
            $collection = collect($ujian->question);
            $shuffled = $collection->shuffle();
            $first = $shuffled->first();
            $all = $shuffled->all();
            Storage::disk('public')->put($json_file_all, $shuffled->toJson());
        }
        //$jawaban_siswa = json_decode($jawaban_siswa);
        //$all = collect($all);
        $first = $all->where('question_id', $request->soal_id)->first();
        $first = collect($first);
        $first = $first->toJson();
        $first = json_decode($first);
        /*$first = Question::with(['answers', 'user_question' => function($query) use ($user){
            if($user->peserta_didik_id){
                $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
            } else {
                $query->where('ptk_id', $user->ptk_id);
            }
        }])->find($request->soal_id);*/
        $questions = [$first];
        $current_id = $first->question_id;
        $user_exam = User_exam::updateOrCreate(
            [
                'exam_id'   => $first->exam_id,
                'anggota_rombel_id' => ($user->peserta_didik) ? $user->peserta_didik->anggota_rombel->anggota_rombel_id : NULL,
                'ptk_id' => $user->ptk_id,
                'user_id' => $user->user_id,
            ],
            [
                'status_ujian' => 1
            ]
        );
        if($request->sisa_waktu){
            $user_exam->sisa_waktu = date('H:i:s', strtotime($request->sisa_waktu));
            $user_exam->save();
        }
        $json_file_user_question = 'user_question-'.$user->user_id.'-'.$request->question_id.'.json';
        if($request->has('answer_id')){
            $isUuid = Uuid::isValid($request->answer_id);
            if($isUuid){
                $collect_user_question = collect([
                    'question_id' => $request->question_id,
                    'anggota_rombel_id' => ($user->peserta_didik) ? $user->peserta_didik->anggota_rombel->anggota_rombel_id : NULL,
                    'ptk_id' => $user->ptk_id,
                    'user_id' => $user->user_id,
                    'user_exam_id' => $user_exam->user_exam_id,
                    'answer_id' => $request->answer_id,
                    'ragu' => $request->ragu,
                    'nomor_urut' => $request->page + 1,
                ]);
                Storage::disk('public')->put($json_file_user_question, $collect_user_question->toJson());
            }
            /*User_question::updateOrCreate(
                [
                    'question_id' => $request->question_id,
                    'anggota_rombel_id' => ($user->peserta_didik) ? $user->peserta_didik->anggota_rombel->anggota_rombel_id : NULL,
                    'ptk_id' => $user->ptk_id,
                ],
                [
                    'user_exam_id' => $user_exam->user_exam_id,
                    'answer_id' => ($isUuid) ? $request->answer_id : NULL,
                    'ragu' => $request->ragu
                ]
            );*/
        }
        /*$all_files = Storage::disk('public')->files();
        $all_files = collect($all_files)->filter(function ($item) use ($user) {
            // replace stristr with your choice of matching function
            return false !== stristr($item, 'user_question-'.$user->user_id);
        });*/
        $json_file_jawaban = 'user_question-'.$user->user_id.'-'.$request->soal_id.'.json';
        $jawaban_siswa = NULL;
        if(Storage::disk('public')->exists($json_file_jawaban)){
            $jawaban_siswa = Storage::disk('public')->get($json_file_jawaban);
            $jawaban_siswa = json_decode($jawaban_siswa);
        }
        $jumlah_jawaban_siswa = $this->jumlah_jawaban_siswa($user->user_id);
        /*$jumlah_jawaban_siswa = User_question::where(function($query) use($user){
            $query->whereNotNull('answer_id');
            if($user->peserta_didik_id){
                $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
            } else {
                $query->where('ptk_id', $user->ptk_id);
            }
        })->count();*/
        return view('ujian.load_soal', ['questions' => $questions, 'user' => $user, 'page' => $request->page, 'current_id' => $current_id, 'keys' => $request->keys, 'jumlah_jawaban_siswa' => $jumlah_jawaban_siswa, 'jawaban_siswa' => $jawaban_siswa])->render();
    }
    public function token(){
        /*
        $event = Event::where('kode', $user->username)->first();
        if($event){
            $all_ujian = Exam::with('event')->whereAktif(1)->whereHas('event')->get();
        } else {
            $all_ujian = Exam::with('pembelajaran.rombongan_belajar')->whereAktif(1)->whereHas('pembelajaran', function($query) use ($user){
                if($user->peserta_didik_id){
                    $query->where('rombongan_belajar_id', $user->peserta_didik->anggota_rombel->rombongan_belajar_id);
                }
            })->get();//->whereAktif(1)->doesntHave('event')->get();
        }
        dump($event);
        dd($all_ujian);*/
        $ujian = '';
        //$mata_ujian = Exam::find(config('global.exam_id'));
        $user = auth()->user();
        $all_ujian = Exam::with('pembelajaran.rombongan_belajar')->whereAktif(1)->whereHas('pembelajaran', function($query) use ($user){
            if($user->peserta_didik_id){
                $query->where('rombongan_belajar_id', $user->peserta_didik->anggota_rombel->rombongan_belajar_id);
            }
        })->get();
        if(!$all_ujian->count()){
            $all_ujian = Exam::with('event')->whereAktif(1)->whereHas('event')->get();
        }        
        $mata_ujian = Exam::find(config('global.exam_id'));
        return view('ujian.token', compact('user', 'ujian', 'mata_ujian', 'all_ujian'));
    }
    public function konfirmasi(Request $request){
        $user = auth()->user();
        $ujian = '';
        $mata_ujian = Exam::find($request->exam_id);
        if($mata_ujian){
            if($mata_ujian->token == $request->token){
                $find = User_exam::where(function($query) use ($user, $request){
                    /*if($user->peserta_didik_id){
                        $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
                    } else {
                        $query->where('ptk_id', $user->ptk_id);
                    }*/
                    $query->where('user_id', $user->user_id);
                    $query->where('exam_id', $request->exam_id);
                    $query->where('status_ujian', 0);
                })->first();
                if($find){
                    $response = [
                        'exam_id' => $request->exam_id,
                        'status' => 'Pengguna '.$user->username.' terdeteksi Anda pernah mengikuti Mata Ujian '.$mata_ujian->nama.'. Silahkan hubungi Proktor',
                        'icon' => 'error',
                        'success' => FALSE
                    ];
                } else {
                    $response = [
                        'exam_id' => $request->exam_id,
                        'status' => NULL,
                        'icon' => NULL,
                        'success' => TRUE
                    ];
                }
            } else {
                $response = [
                    'exam_id' => NULL,
                    'status' => 'Token salah. Silahkan hubungi proktor',
                    'icon' => 'error',
                    'success' => FALSE
                ];
            }
        } else {
            $response = [
                'exam_id' => NULL,
                'status' => 'Mata ujian tidak ditemukan',
                'icon' => 'error',
                'success' => FALSE
            ];
        }
        return response()->json($response);
    }
    public function selesai(Request $request){
        $user = auth()->user();
        $ujian_id = $request->ujian_id;
        $question_id = $request->question_id;
        $answer_id = $request->answer_id;
        $user_exam = User_exam::firstOrCreate(
            [
                'exam_id'   => $ujian_id,
                'anggota_rombel_id' => ($user->peserta_didik) ? $user->peserta_didik->anggota_rombel->anggota_rombel_id : NULL,
                'ptk_id' => $user->ptk_id
            ]
        );
        if($request->sisa_waktu){
            $user_exam->sisa_waktu = date('H:i:s', strtotime($request->sisa_waktu));
            $user_exam->status_ujian = 0;
            $user_exam->save();
        }
        if($request->has('answer_id')){
            $isUuid = Uuid::isValid($request->answer_id);
            User_question::updateOrCreate(
                [
                    'question_id' => $question_id,
                    'anggota_rombel_id' => ($user->peserta_didik) ? $user->peserta_didik->anggota_rombel->anggota_rombel_id : NULL,
                    'ptk_id' => $user->ptk_id
                ],
                [
                    'user_exam_id' => $user_exam->user_exam_id,
                    'answer_id' => ($isUuid) ? $request->answer_id : NULL,
                    'user_id' => $user->user_id,
                ]
            );
        }
        //$all_files = Storage::disk('public')->files();
        $path = public_path('storage');
        $all_files = File::allfiles($path);
        $all_files = collect($all_files)->filter(function ($item) use ($user) {
            // replace stristr with your choice of matching function
            return false !== stristr($item, 'user_question-'.$user->user_id);
        });
        if($all_files->count()){
            foreach($all_files as $file){
                if(Storage::disk('public')->exists($file)){
                    $user_question = Storage::disk('public')->get($file);
                    $user_question = json_decode($user_question);
                    try {
                        User_question::updateOrCreate(
                            [
                                'question_id' => $user_question->question_id,
                                'anggota_rombel_id' => $user_question->anggota_rombel_id,
                                'ptk_id' => $user_question->ptk_id,
                            ],
                            [
                                'user_exam_id' => $user_question->user_exam_id,
                                'answer_id' => $user_question->answer_id,
                                'ragu' => $user_question->ragu,
                                'nomor_urut' => $user_question->nomor_urut,
                                'user_id' => $user_question->user_id,
                            ]
                        );
                    } catch (\Exception $e) {
                        //
                    }
                    Storage::disk('public')->delete($file);
                }
            }
        }
        $json_file_all = 'all-'.$user->user_id.'-'.$ujian_id.'.json';
        $json_file_ujian = 'ujian-'.$user->user_id.'-'.$ujian_id.'.json';
        Storage::disk('public')->delete([$json_file_all, $json_file_ujian]);
        $response = [
            'title' => 'Berhasil',
            'text' => 'Nilai berhasil disimpan',
            'icon' => 'success',
        ];
        return response()->json($response);
    }
    public function detil_hasil(Request $request){
        $user = auth()->user();
        $ujian = Exam::with(['user_exam' => function($query) use ($user){
            $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
        }])->find($request->route('id'));
        return view('ujian.detil-hasil', compact('ujian', 'user'));
    }
    public function soal(Request $request){
        $user = auth()->user();
        $ujian = Exam::find($request->ujian_id);
        return view('ujian.soal', compact('ujian', 'user'));
    }
    public function hasil(Request $request){
        $user = auth()->user();
        return view('ujian.hasil', compact('user'));
    }
    public function tambah_data(Request $request){
        $user = auth()->user();
        $query = $request->route('query');
        if($query == 'soal'){
            return $this->tambah_soal($request, $user);
        } else {
            echo 'fungsi '.$query.' belum ada!';
        }
    }
    public function tambah_soal($request, $user){
        $ujian = Exam::find($request->ujian_id);
        return view('ujian.tambah_soal', compact('ujian', 'user'));
    }
    public function insert_soal(Request $request){
        $ujian_id = $request->route('ujian_id');
        $exam = Exam::withCount('question')->find($ujian_id);
        if($exam->question_count < $exam->jumlah_soal){
            $id = $request->route('id');
            $bank_soal = Bank_soal::with('jawaban')->find($id);
            $simpan_soal = Question::updateOrCreate(
                [
                    'exam_id' => $ujian_id,
                    'soal_ke' => $bank_soal->soal_ke
                ],
                [
                    'bank_soal_id' => $bank_soal->bank_soal_id,
                    'question' => $bank_soal->soal
                ]
            );
            $simpan_jawaban = 0;
            if($simpan_soal){
                foreach($bank_soal->jawaban as $jawaban){
                    $simpan_jawaban++;
                    Answer::updateOrCreate(
                        [
                            'question_id' => $simpan_soal->question_id,
                            'jawaban_ke' => $jawaban->jawaban_ke
                        ],
                        [
                            'answer' => $jawaban->jawaban,
                            'correct' => $jawaban->benar
                        ]
                    );
                }
                if($simpan_jawaban){
                    $response = [
                        'status' => 'Bank Soal berhasil ditambahkan di Mata Ujian',
                        'success' => true,
                        'icon' => 'success'
                    ];
                } else {
                    $response = [
                        'status' => 'Bank Soal gagal ditambahkan di Mata Ujian',
                        'success' => false,
                        'icon' => 'error'
                    ];
                }
            } else {
                $response = [
                    'status' => 'Bank Soal gagal ditambahkan di Mata Ujian',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
        } else {
            $response = [
                'status' => 'Jumlah Soal sudah lengkap',
                'success' => false,
                'icon' => 'error'
            ];
            
        }
        return response()->json($response);
    }
    public function all_ujian(){
        $user = auth()->user();
        return view('materi.ujian.index', compact('user'));
    }
    public function test(Request $request){
        /*$user = auth()->user();
        $key = 'ujian-'.$user->user_id.'-d306c507-a8f6-4f87-92e3-cbd2246b45aa';
        $path = base_path('script_python');
        echo "$path/prepare_jawaban.py";
        dd($path);
        $process = new Process("python $path/prepare_jawaban.py ".$user->user_id);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        echo $process->getOutput();*/
    }
}
