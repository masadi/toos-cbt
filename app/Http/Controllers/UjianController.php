<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use App\Support\Collection;
use App\Ptk;
use App\Exam;
use App\Question;
use App\Bank_soal;
use App\Answer;
use App\User_exam;
use App\User_question;
use App\Event;
use File;
use Helper;
use Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class UjianController extends Controller
{
    public function index(Request $request){
        $user = auth()->user();
        $ujian_id = $request->ujian_id;
        $find = User_exam::with('exam')->where('exam_id', $ujian_id)->where('user_id', $user->user_id)->first();
        $path = storage_path('app/public/'.$ujian_id.'.json');
        $path_jawaban_siswa = storage_path('app/public/'.$user->user_id.'.json');
        /*$cache_ujian = Cache::store('file')->get('ujian');
        if(!$cache_ujian){
            Cache::store('file')->put('ujian', $get_ujian);
        }*/
        if(!File::exists($path_jawaban_siswa)){
            File::put($path_jawaban_siswa, json_encode([]));
        }
        if($find){
            $user_exam = $find;
            if($find->daftar_soal){
                $daftar_soal = unserialize($find->daftar_soal);
                $collection_daftar_soal = collect($daftar_soal);
            } else {
                $ujian = Helper::soal_reader($ujian_id);
                if($ujian){
                    $make_shuffle = collect($ujian->question);
                    $shuffled = $make_shuffle->shuffle();
                    $shuffled_question = $shuffled->toArray();
                    $make_keyed = collect($shuffled_question);
                    $keyed = $make_keyed->keyBy('question_id');
                    $collection = collect($keyed->all());
                    $keys = $collection->keys();
                    $daftar_soal = $keys->all();
                    $find->daftar_soal = serialize($daftar_soal);
                    $find->save();
                    $collection_daftar_soal = collect($daftar_soal); 
                } else {
                    return redirect()->route('home')->with(['error' => 'File ujian tidak ditemukan, silahkan hubungi proktor']);
                }
            }
        } else {
            $ujian = Helper::soal_reader($ujian_id);
            if($ujian){
                $make_shuffle = collect($ujian->question);
                $shuffled = $make_shuffle->shuffle();
                $shuffled_question = $shuffled->toArray();
                $make_keyed = collect($shuffled_question);
                $keyed = $make_keyed->keyBy('question_id');
                $collection = collect($keyed->all());
                $keys = $collection->keys();
                $daftar_soal = $keys->all();
                $user_exam = User_exam::create([
                    'exam_id'   => $ujian_id,
                    'user_id' => $user->user_id,
                    'status_ujian' => 1,
                    'daftar_soal' => serialize($daftar_soal),
                ]);
                $collection_daftar_soal = collect($daftar_soal); 
            } else {
                return redirect()->route('home')->with(['error' => 'File ujian tidak ditemukan, silahkan hubungi proktor']);
            }
        }
        $json_reader = Helper::json_reader($path, $collection_daftar_soal->first());
        $ujian = $json_reader->ujian;
        $all = $json_reader->all;
        $first = $json_reader->first;
        $questions = [$first];
        $current_id = $first->question_id;
        $keys = $daftar_soal;
        $path_jawaban_siswa = storage_path('app/public/'.$user->user_id.'.json');
        $jawaban_reader = Helper::jawaban_reader($user->user_id);
        $collect_jawaban = collect($jawaban_reader);
        $jawaban_siswa = $collect_jawaban->where('question_id', $current_id)->first();
        $jawaban_siswa = ($jawaban_siswa) ? (object) $jawaban_siswa : NULL;
        $jumlah_jawaban_siswa = $collect_jawaban->whereNull('ragu')->count();
        $now = Carbon::now($user->timezone)->toDateTimeString();
        return view('ujian.proses_ujian', compact('ujian', 'questions', 'user_exam', 'user', 'jumlah_jawaban_siswa', 'jawaban_siswa', 'all', 'page', 'keys', 'current_id', 'reader', 'now', 'collect_jawaban'));
    }
    public function get_soal(Request $request){
        if($request->ajax()){
            $user_id = $request->user_id;
            
            if(!$request->ujian_id){
                $output = [
                    'ujian' => 1,
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Permintaan tidak sah'
                ];
                return response()->json($output);
            }
            $user_exam = User_exam::where('exam_id', $request->ujian_id)->where('user_id', $user_id)->first();
            if($request->sisa_waktu){
                $user_exam->sisa_waktu = date('H:i:s', strtotime($request->sisa_waktu));
                $user_exam->save();
            }
            if(!$user_exam->status_ujian){
                $output = [
                    'ujian' => 0,
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Ujian Selesai'
                ];
                return response()->json($output);
            }
            $path_jawaban_siswa = storage_path('app/public/'.$user_id.'.json');
            if($request->has('answer_id')){
                $jawaban_reader = Helper::jawaban_reader($user_id);
                //dd($jawaban_reader);
                $isi_jawaban = [];
                if($jawaban_reader){
                    foreach($jawaban_reader as $key => $array){
                        $isi_jawaban[$array['question_id']] = $array;
                    }
                }
                $isUuid = Uuid::isValid($request->answer_id);
                if($isUuid){
                    $collect_user_question = collect([
                        'question_id' => $request->question_id,
                        'user_id' => $user_id,
                        'user_exam_id' => $user_exam->user_exam_id,
                        'answer_id' => $request->answer_id,
                        'ragu' => $request->ragu,
                        'nomor_urut' => $request->page + 1,
                    ]);
                    $isi_jawaban[$request->question_id] = $collect_user_question->toArray();
                    File::put($path_jawaban_siswa, json_encode($isi_jawaban));
                }
            }
            $path = storage_path('app/public/'.$request->ujian_id.'.json');
            $json_reader = Helper::json_reader($path, $request->soal_id);
            $ujian = $json_reader->ujian;
            $all = $json_reader->all;
            $first = $json_reader->first;
            $questions = [$first];
            $current_id = $first->question_id;
            $keys = unserialize($user_exam->daftar_soal);
            $jawaban_reader = Helper::jawaban_reader($user_id);
            $collect_jawaban = collect($jawaban_reader);
            $jawaban_siswa = $collect_jawaban->where('question_id', $current_id)->first();
            $jawaban_siswa = ($jawaban_siswa) ? (object) $jawaban_siswa : NULL;
            $jumlah_jawaban_siswa = $collect_jawaban->whereNull('ragu')->count();
            $output = [
                'html' => view('ujian.load_soal', ['questions' => $questions, 'page' => $request->page, 'current_id' => $current_id, 'keys' => $request->keys, 'jumlah_jawaban_siswa' => $jumlah_jawaban_siswa, 'jawaban_siswa' => $jawaban_siswa])->render(),
                'current_id' => $current_id,
            ];
            return response()->json($output);
        } else {
            return view('ujian.tolak');
        }
    }
    public function selesai(Request $request){
        $messages = [
            'ujian_id.required' => 'ID Mata Ujian tidak boleh kosong',
            'question_id.required' => 'ID Soal tidak boleh kosong',
            'answer_id.uuid' => 'Jawaban tidak boleh kosong',
        ];
        $validator = Validator::make($request->all(), [
            'ujian_id' => 'required',
            'question_id' => 'required',
            'answer_id' => 'uuid',
         ],
        $messages
        );
        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = [
                'title' => 'Gagal',
                'text' => $errors->first('answer_id'),
                'icon' => 'error',
            ];
            return response()->json($response);
        }
        $ujian_id = $request->ujian_id;
        $question_id = $request->question_id;
        $answer_id = $request->answer_id;
        $user_exam = User_exam::where('exam_id', $request->ujian_id)->where('user_id', $request->user_id)->first();
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
                    'user_id' => $request->user_id,
                ],
                [
                    'user_exam_id' => $user_exam->user_exam_id,
                    'answer_id' => ($isUuid) ? $request->answer_id : NULL,
                ]
            );
        }
        $jawaban_reader = Helper::jawaban_reader($request->user_id);
        $collect_jawaban = collect($jawaban_reader);
        if($collect_jawaban->count()){
            foreach($collect_jawaban as $jawaban){
                $user_question = collect($jawaban);
                $user_question = json_decode($user_question->toJson());
                User_question::updateOrCreate(
                     [
                        'question_id' => $user_question->question_id,
                        'user_id' => $user_question->user_id,
                    ],
                    [
                        'user_exam_id' => $user_question->user_exam_id,
                        'answer_id' => $user_question->answer_id,
                        'ragu' => $user_question->ragu,
                        'nomor_urut' => $user_question->nomor_urut,
                    ]
                );
            }
            $path = storage_path('app/public/'.$request->user_id.'.json');
            File::delete($path);
        }
        $response = [
            'title' => 'Berhasil',
            'text' => 'Nilai berhasil disimpan',
            'icon' => 'success',
        ];
        return response()->json($response);
    }
    public function indexOld(Request $request){
        $ujian = Cache::store('file')->get('ujian');
        Cache::store('file')->put('ujian', $get_ujian);
        $get_ujian = Cache::store('file')->get('get_ujian');
        $user = auth()->user();
        $now = Carbon::now($user->timezone)->toDateTimeString();
        $reader = new JsonReader();
        $ujian_id = $request->ujian_id;
        $user_folder = Helper::user_folder($user->user_id);
        $exam_folder = Helper::exam_folder($user->user_id, $ujian_id);
        if (!File::isDirectory($user_folder)) {
            File::makeDirectory($user_folder);
        }
        if (!File::isDirectory($exam_folder)) {
            File::makeDirectory($exam_folder);
        }
        if(!$ujian || !$get_ujian){
            $json_ujian = $user_folder.'/'.$ujian_id.'.json';
            if(File::exists($json_ujian)){
                $reader->open($json_ujian);
                $get_ujian = '';
                if($reader->read()) {
                    $collection = collect($reader->value());
                    $get_ujian = $collection->toJson();
                    $get_ujian = json_decode($get_ujian);
                }
            } else {
                $get_ujian = Exam::withCount(['question', 'user_question' => function($query) use ($user){
                    $query->where('user_questions.user_id', $user->user_id);
                }])->with(['question' => function($query){
                    $query->with('answers');
                    $query->orderBy('soal_ke');
                }, 'user_exam' => function($query) use ($user){
                    $query->where('user_exams.user_id', $user->user_id);
                }])->find($ujian_id);
                $collection = collect($get_ujian->question);
                $shuffled = $collection->shuffle();
                $questions = $shuffled->toArray();
                unset($get_ujian->question);
                $exam_json = [
                    'exam' => $get_ujian->toArray(),
                    'questions' => $questions,
                ];
                $gabung = collect($exam_json);
                Cache::store('file')->put('ujian', $get_ujian);
                Cache::store('file')->put('get_ujian', $gabung);
                File::put($user_folder.'/'.$ujian_id.'.json', $gabung->toJson());
            }
        }
        $jumlah_jawaban_siswa = $this->jumlah_jawaban_siswa($user->user_id, $ujian_id);
        $ujian = $get_ujian->exam;
        $waktu_ujian = time() + ($ujian->durasi * 60);
        if($ujian->question_count){
            $json_file_all = 'all-'.$user->user_id.'-'.$ujian_id.'.json';
            $all = collect($get_ujian->questions);
            $first = $all->first();
            $all = $all->all();
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
            $i=1;
            foreach($all as $s){
                $keys[] = $s->question_id;
                $i++;
            }
            $jawaban_siswa = NULL;
            if(File::exists($exam_folder.'/'.$current_id.'.json')){
                $reader->open($exam_folder.'/'.$current_id.'.json');
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
    private function jumlah_jawaban_siswa($user_id, $exam_id){
        $exam_folder = Helper::exam_folder($user_id, $exam_id);
        $all_files = File::allfiles($exam_folder);
        $all_files = collect($all_files);
        return $all_files->count();
    }
    public function get_soalOld(Request $request){
        if($request->ajax()){
            $reader = new JsonReader();
            $user = auth()->user();
            
            if(!$request->ujian_id){
                $output = [
                    'ujian' => 1,
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Permintaan tidak sah'
                ];
                return response()->json($output);
            }
            $user_exam = User_exam::where('exam_id', $request->ujian_id)->where('user_id', $user->user_id)->first();
            if($request->sisa_waktu){
                $user_exam->sisa_waktu = date('H:i:s', strtotime($request->sisa_waktu));
                $user_exam->save();
            }
            if(!$user_exam->status_ujian){
                $output = [
                    'ujian' => 0,
                    'icon' => 'error',
                    'title' => 'Gagal',
                    'text' => 'Ujian Selesai'
                ];
                return response()->json($output);
            }
            $all = Cache::store('file')->get('get_soal');
            $user_folder = Helper::user_folder($user->user_id);
            $exam_folder = Helper::exam_folder($user->user_id, $request->ujian_id);
            $path = $user_folder.'/'.$request->ujian_id.'.json';
            $test = 'cache';
            if(!$all){
                $test = 'file';
                $reader->open($path);
                if ($reader->read()) {
                    $collection = collect($reader->value());
                    $all = $collection->toJson();
                    $all = json_decode($all);
                    $all = collect($all->questions);
                    Cache::store('file')->put('get_soal', $all);
                    $first = $all->first();
                }
            }
            $first = $all->where('question_id', $request->soal_id)->first();
            $first = collect($first);
            $first = $first->toJson();
            $first = json_decode($first);
            $questions = [$first];
            $current_id = $first->question_id;
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
                    File::put($exam_folder.'/'.$request->question_id.'.json', $collect_user_question->toJson());
                }
            }
            $jumlah_jawaban_siswa = $this->jumlah_jawaban_siswa($user->user_id, $request->ujian_id);
            $jawaban_siswa = NULL;
            if(File::exists($exam_folder.'/'.$request->soal_id.'.json')){
                $reader->open($exam_folder.'/'.$request->soal_id.'.json');
                if ($reader->read()) {
                    $jawaban_siswa = collect($reader->value());
                    $jawaban_siswa = $jawaban_siswa->toJson();
                }
                $jawaban_siswa = json_decode($jawaban_siswa);
            }
            $output = [
                'html' => view('ujian.load_soal', ['questions' => $questions, 'user' => $user, 'page' => $request->page, 'current_id' => $current_id, 'keys' => $request->keys, 'jumlah_jawaban_siswa' => $jumlah_jawaban_siswa, 'jawaban_siswa' => $jawaban_siswa])->render(),
                'current_id' => $current_id,
                'test' => $test,
            ];
            return response()->json($output);
        } else {
            return view('ujian.tolak');
        }
    }
    public function token(Request $request){
        $exam_id = $request->route('ujian_id');
        $exam = Exam::find($exam_id);
        $modal_s = 'modal-standart';
        return view('ujian.token', compact('exam', 'modal_s'));
    }
    public function konfirmasi(Request $request){
        $user = auth()->user();
        $ujian = '';
        $mata_ujian = Exam::find($request->exam_id);
        if($mata_ujian){
            if($mata_ujian->token == $request->token){
                $find = User_exam::where(function($query) use ($user, $request){
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
    public function detil_hasil(Request $request){
        $user = auth()->user();
        $ujian = Exam::with(['user_exam' => function($query) use ($user){
            $query->where('user_id', $user->user_id);
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
    public function simpan_ujian(Request $request){
        dd($request->all());
    }
}
