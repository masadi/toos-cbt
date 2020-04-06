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
class UjianController extends Controller
{
    public function index(Request $request){
        $user = auth()->user();
        $ujian_id = $request->ujian_id;
        $ujian = Exam::withCount(['question', 'list_soal', 'user_question' => function($query) use ($user){
            if($user->peserta_didik_id){
                $query->where('user_exams.anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
            } else {
                $query->where('user_exams.ptk_id', $user->ptk_id);
            }
        }])->with(['question' => function($query){
            $query->orderBy('soal_ke');
        }, 'user_exam' => function($query) use ($user){
            if($user->peserta_didik_id){
                $query->where('user_exams.anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
            } else {
                $query->where('user_exams.ptk_id', $user->ptk_id);
            }
        }])->find($ujian_id);
        $jumlah_jawaban_siswa = (isset($ujian->user_question_count)) ? $ujian->user_question_count : 0;
        $waktu_ujian = time() + ($ujian->durasi * 60);
        if($ujian->question->count()){
            $collection = collect($ujian->question);
            $shuffled = $collection->shuffle();
            $first = $shuffled->first();
            $all = $shuffled->all();
            $questions = [$first];
            $current_id = $first->question_id;
            $page = 0;
            $user_exam = User_exam::updateOrCreate(
                [
                    'exam_id'   => $ujian_id,
                    'anggota_rombel_id' => ($user->peserta_didik) ? $user->peserta_didik->anggota_rombel->anggota_rombel_id : NULL,
                    'ptk_id' => $user->ptk_id,
                ],
                [
                    'status_ujian' => 1
                ]
            );
            if($ujian->user_question_count){
                $all = User_question::where(function($query) use ($user, $ujian_id){
                    $query->whereHas('user_exam', function($sq) use ($ujian_id){
                        $sq->where('exam_id', $ujian_id);
                    });
                    if($user->peserta_didik_id){
                        $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
                    } else {
                        $query->where('ptk_id', $user->ptk_id);
                    }
                })->orderBy('nomor_urut')->get();
                foreach($all as $s){
                    $keys[] = $s->question_id;
                }
                $questions = [$all[0]];
                $current_id = $all[0]->question_id;
            } else {
                $keys = [];
                $i=1;
                foreach($all as $s){
                    User_question::updateOrCreate(
                        [
                            'question_id' => $s->question_id,
                            'user_exam_id' => $user_exam->user_exam_id,
                        ],
                        [
                            'anggota_rombel_id' => ($user->peserta_didik) ? $user->peserta_didik->anggota_rombel->anggota_rombel_id : NULL,
                            'ptk_id' => $user->ptk_id,
                            'nomor_urut' => $i,
                        ]
                    );
                    $keys[] = $s->question_id;
                    $i++;
                }
            }
            return view('ujian.proses_ujian', compact('ujian', 'questions', 'user_exam', 'user', 'jumlah_jawaban_siswa', 'all', 'page', 'keys', 'current_id'));
        } else {
            $ujian = '';
            return view('ujian.soal_tidak_lengkap', compact('ujian'));
        }
    }
    public function get_soal(Request $request){
        //dump($request->all());
        $user = auth()->user();
        $first = Question::with(['answers', 'user_question' => function($query) use ($user){
            if($user->peserta_didik_id){
                $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
            } else {
                $query->where('ptk_id', $user->ptk_id);
            }
        }])->find($request->soal_id);
        $questions = [$first];
        $current_id = $first->question_id;
        $user_exam = User_exam::updateOrCreate(
            [
                'exam_id'   => $first->exam_id,
                'anggota_rombel_id' => ($user->peserta_didik) ? $user->peserta_didik->anggota_rombel->anggota_rombel_id : NULL,
                'ptk_id' => $user->ptk_id,
            ],
            [
                'status_ujian' => 1
            ]
        );
        if($request->sisa_waktu){
            $user_exam->sisa_waktu = date('H:i:s', strtotime($request->sisa_waktu));
            $user_exam->save();
        }
        if($request->has('answer_id')){
            $isUuid = Uuid::isValid($request->answer_id);
            User_question::updateOrCreate(
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
            );
        }
        $jumlah_jawaban_siswa = User_question::where(function($query) use($user){
            $query->whereNotNull('answer_id');
            if($user->peserta_didik_id){
                $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
            } else {
                $query->where('ptk_id', $user->ptk_id);
            }
        })->count();
        return view('ujian.load_soal', ['questions' => $questions, 'user' => $user, 'page' => $request->page, 'current_id' => $current_id, 'keys' => $request->keys, 'jumlah_jawaban_siswa' => $jumlah_jawaban_siswa])->render();
    }
    public function token(){
        $user = auth()->user();
        $ujian = '';
        $mata_ujian = Exam::find(config('global.exam_id'));
        $all_ujian = Exam::with('pembelajaran')->whereAktif(1)->whereHas('pembelajaran', function($query) use ($user){
            if($user->peserta_didik_id){
                $query->where('rombongan_belajar_id', $user->peserta_didik->anggota_rombel->rombongan_belajar_id);
            }
        })->get();
        if(!$all_ujian->count()){
            $all_ujian = Exam::with('event')->whereAktif(1)->whereHas('event')->get();
        }
        return view('ujian.token', compact('user', 'ujian', 'mata_ujian', 'all_ujian'));
    }
    public function konfirmasi(Request $request){
        $user = auth()->user();
        $ujian = '';
        $mata_ujian = Exam::find($request->exam_id);
        if($mata_ujian){
            if($mata_ujian->token == $request->token){
                $find = User_exam::where(function($query) use ($user, $request){
                    if($user->peserta_didik_id){
                        $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
                    } else {
                        $query->where('ptk_id', $user->ptk_id);
                    }
                    $query->where('exam_id', $request->exam_id);
                    $query->where('status_ujian', 0);
                })->first();
                if($find){
                    $response = [
                        'exam_id' => $request->exam_id,
                        'status' => 'Terdeteksi Anda pernah mengikuti Mata Ujian ini. Silahkan hubungi Proktor',
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
                ]
            );
        }
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
}
