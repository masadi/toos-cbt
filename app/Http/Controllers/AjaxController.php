<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mst_wilayah;
use App\Sekolah;
use App\Ptk;
use App\Peserta_didik;
use App\Rombongan_belajar;
use App\Pembelajaran;
use App\Bank_soal;
use App\Mata_pelajaran_kurikulum;
use App\Kurikulum;
use App\Tingkat_pendidikan;
use App\Mata_pelajaran;
use App\Exam;
use App\Question;
use App\Server;
use App\Anggota_rombel;
use App\User;
use App\User_exam;
use App\User_question;
use App\Event;
use DataTables;
use Str;
use Helper;
use Carbon\Carbon;
class AjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
 
    }
    public function get_all_data(Request $request){
        $query = $request->route('query');
        if($query == 'sekolah'){
            return $this->get_all_sekolah($request);
        } elseif($query == 'ptk'){
            return $this->get_all_ptk($request);
        } elseif($query == 'peserta-didik'){
            return $this->get_all_peserta_didik($request);
        } elseif($query == 'rombongan-belajar'){
            return $this->get_all_rombongan_belajar($request);
        } elseif($query == 'mata-pelajaran-kurikulum'){
            return $this->get_all_mata_pelajaran_kurikulum($request);
        } elseif($query == 'mata-pelajaran'){
            return $this->get_all_mata_pelajaran($request);
        } elseif($query == 'server'){
            return $this->get_all_server($request);
        } elseif($query == 'users'){
            return $this->get_all_users($request);
        } elseif($query == 'status-peserta'){
            return $this->get_all_status_peserta($request);
        } elseif($query == 'reset-login'){
            return $this->reset_login($request);
        } elseif($query == 'ujian-aktif'){
            return $this->ujian_aktif($request);
        } else {
            echo 'fungsi '.$query.' belum ada!';
        }
    }
    public function get_all_materi(Request $request){
        $query = $request->route('query');
        if($query == 'bank-soal'){
            return $this->get_all_bank_soal($request);
        } elseif($query == 'ptk'){
            return $this->get_all_ptk($request);
        } elseif($query == 'peserta-didik'){
            return $this->get_all_peserta_didik($request);
        } elseif($query == 'rombongan-belajar'){
            return $this->get_all_rombongan_belajar($request);
        } elseif($query == 'ujian'){
            return $this->get_all_ujian($request);
        } elseif($query == 'hasil-ujian'){
            return $this->get_all_hasil_ujian($request);
        } else {
            echo 'fungsi '.$query.' belum ada!';
        }
    }
    public function ujian_aktif($request){
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        if($event){
            $query = Exam::whereAktif(1)->whereHas('event', function($query) use ($event){
                $query->where('event_id', $event->id);
            })->with(['pembelajaran.rombongan_belajar']);
        } else {
            $query = Exam::whereAktif(1)->whereHas('pembelajaran', function($query) use ($user){
                $query->where('sekolah_id', $user->sekolah_id);
            })->with(['pembelajaran.rombongan_belajar']);
        }
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('status', function ($item) {
            $links = '-';
            return $links;
        })
        ->addColumn('toggle', function ($item) {
            $links = '<a data-exam_id="'.$item->exam_id.'" href="'.route('proktor.index', ['query' => 'toggle-ujian']).'" class="btn btn-sm btn-block btn-danger toggle-reset">Non Aktifkan</a>';
            return $links;
        })
        ->rawColumns(['status', 'toggle'])
        ->make(true);
    }
    public function reset_login($request){
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $query = User::query()->with(['ptk.user_exam.exam', 'peserta_didik.anggota_rombel.rombongan_belajar', 'peserta_didik.anggota_rombel.user_exam.exam'])->where(function($query) use ($user, $event){
            $query->where('logout', FALSE);
            /*$query->whereRoleIs('peserta_didik');
            $query->orWhereRoleIs('ptk');
            $query->where('logout', FALSE);*/
            $query->where('user_id', '<>', $user->user_id);
            if($event){
                $sekolah_id = $event->peserta->pluck('sekolah_id');
                $query->whereIn('sekolah_id', $sekolah_id->all());
            } else {
                $query->where('sekolah_id', $user->sekolah_id);
            }
        });
        return DataTables::of($query)
        ->addColumn('checkbox', function ($item) {
            //dd($item);
            $links = '<input type="checkbox" name="user_id[]" value="'.$item->user_id.'">';
            return $links;
        })
        ->addColumn('reset_login', function ($item) {
            $links = '<a href="'.route('proktor.reset_login', ['user_id' => $item->user_id]).'" class="btn btn-sm btn-block btn-danger reset_login">Reset</a>';
            return $links;
        })
        ->addColumn('nama_rombongan_belajar', function ($item) {
            if($item->peserta_didik){
                $links = ($item->peserta_didik->anggota_rombel->rombongan_belajar) ? $item->peserta_didik->anggota_rombel->rombongan_belajar->nama : '-';
            } else {
                $links = '-';
            }
            return $links;
        })
        ->addColumn('mata_ujian', function ($item) {
            if($item->peserta_didik){
                $links = ($item->peserta_didik->anggota_rombel->user_exam) ? $item->peserta_didik->anggota_rombel->user_exam->exam->nama : '-';
            } else {
                $links = ($item->ptk->user_exam) ? $item->ptk->user_exam->exam->nama : '-';
            }
            return $links;
        })
        ->rawColumns(['checkbox', 'reset_login'])
        ->make(true);
    }
    public function get_all_status_peserta($request){
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $query = User_exam::whereHas('exam', function($query) use ($event, $user){
            if($event){
                $query->whereHas('event', function($query) use ($event){
                    $query->where('events.id', $event->id);
                });
            } else {
                $query->whereHas('pembelajaran', function($query) use ($user){
                    $query->where('sekolah_id', $user->sekolah_id);
                });
            }
            $query->whereAktif(1);
        })->with(['exam.pembelajaran.rombongan_belajar', 'user'])->orderBy('status_ujian', 'DESC')->orderBy('status_upload', 'DESC');
        return DataTables::eloquent($query)
        ->startsWithSearch()
        ->filter(function ($query) use ($request) {
            if($request->sekolah_id){
                $query->whereHas('anggota_rombel', function($query) use ($request) {
                    $query->where('sekolah_id', $request->sekolah_id);
                });
            }
            if($request->rombongan_belajar_id){
                $query->whereHas('anggota_rombel', function($query) use ($request) {
                    $query->where('rombongan_belajar_id', $request->rombongan_belajar_id);
                });
            }
            if($request->search['value']){
                $query->whereIn('user_id', function($query) use ($request){
                    //$query->select('user_id')->from('users')->whereRaw("LOWER(name) like '%?%'", [strtolower($request->search['value'])]);
                    $query->select('user_id')->from('users')->where('name', 'like', '%'.$request->search['value'].'%');
                });
            }
        })
        ->orderColumn('status_ujian', function ($query, $order) {
            $query->orderBy('status_ujian', $order);
        })
        ->addIndexColumn()
        ->addColumn('rombongan_belajar', function ($item) use ($request){
            $output['query'] = FALSE;
            if($request->sekolah_id){
                $output['query'] = TRUE;
                $rombongan_belajar = Rombongan_belajar::where('sekolah_id', $request->sekolah_id)->get();
                if($rombongan_belajar->count()){
                    foreach($rombongan_belajar as $rombel){
                        $record= array();
                        $record['id'] 	= $rombel->rombongan_belajar_id;
                        $record['text'] 	= $rombel->nama;
                        $output['result'][] = $record;
                    }
                } else {
                    $record['id'] 	= '';
                    $record['text'] 	= 'Tidak ditemukan data rombongan belajar';
                    $output['result'][] = $record;
                }
            }
            if($request->rombongan_belajar_id){
                $output['query'] = FALSE;
            }
            return $output;
        })
        ->addColumn('checkbox', function ($item) {
            if($item->status_ujian){
                /*if($item->anggota_rombel_id){
                    $links = '<input type="checkbox" name="anggota_rombel_id[]" value="'. $item->anggota_rombel_id .'" disabled>';
                } else {
                    $links = '<input type="checkbox" name="ptk_id[]" value="'. $item->ptk_id .'" disabled>';
                }*/
                $links = '<input type="checkbox" name="user_exam_id[]" value="'. $item->user_exam_id .'" disabled>';
            } else {
                if($item->status_upload){
                    /*if($item->anggota_rombel_id){
                        $links = '<input type="checkbox" name="anggota_rombel_id[]" value="'. $item->anggota_rombel_id .'" disabled>';
                    } else {
                        $links = '<input type="checkbox" name="ptk_id[]" value="'. $item->ptk_id .'" disabled>';
                    }*/
                    $links = '<input type="checkbox" name="user_exam_id[]" value="'. $item->user_exam_id .'" disabled>';
                } else {
                    /*if($item->anggota_rombel_id){
                        $links = '<input type="checkbox" class="anggota_rombel_id" name="anggota_rombel_id['. $item->user_exam_id .'][]" value="'. $item->anggota_rombel_id .'">';
                    } else {
                        $links = '<input type="checkbox" class="ptk_id" name="ptk_id['. $item->user_exam_id .'][]" value="'. $item->ptk_id .'">';
                    }*/
                    $links = '<input type="checkbox" name="user_exam_id[]" value="'. $item->user_exam_id .'">';
                }
            }
            return $links;
        })
        ->addColumn('detil', function ($item) {
            $links = '<a href="'.route('ujian.detil_hasil', ['id' => $item->exam_id]).'" class="btn btn-sm btn-block btn-warning">Detil</a>';
            return $links;
        })
        //->addColumn('nama', function ($item) {
            //$links = '<input class="user_exam_id" type="hidden" name="user_exam_id[]" value="'. $item->user_exam_id .'">'.$item->user->name;
            //return $links;
        //})
        ->addColumn('user', function (User_exam $item) {
            return $item->user->name;
        })
        /*->addColumn('filter_nama', function ($item) {
            if($item->anggota_rombel){
                $links = $item->anggota_rombel->peserta_didik->nama;
            } else {
                $links = $item->ptk->nama;
            }
            return $links;
        })
        ->addColumn('filter_nama', function (User_exam $User_exam) {
            if($User_exam->ptk){
                return $User_exam->ptk->nama;
            } else {
                return $User_exam->peserta_didik->nama;
            }
        })*/
        ->addColumn('mata_ujian', function ($item) {
            return $item->exam->nama;
        })
        ->addColumn('status_ujian', function ($item) {
            $links = ($item->status_ujian) ? 'Sedang mengerjakan' : 'Selesai';
            return $links;
        })
        ->addColumn('status_upload', function ($item) {
            $links = ($item->status_upload) ? 'Terupload' : 'Belum Terupload';
            return $links;
        })
        ->addColumn('force_selesai', function ($item) {
            if ($item->status_ujian && $item->updated_at->diffInHours(Carbon::now()) > 1) {
                $links = '<a href="'.route('proktor.force_selesai', ['id' => $item->user_exam_id]).'" class="btn btn-sm btn-block btn-danger force_selesai">Force Selesai</a>';
            } else {
                $links = '-';
            }
            //$links = $item->updated_at.'=>'.Carbon::now();
            return $links;
        })
        ->rawColumns(['checkbox', 'nama', 'mata_ujian', 'detil', 'force_selesai'])
        ->make(true);
    }
    public function get_all_hasil_ujian($request){
        $query = User_exam::with('exam.pembelajaran')->where('anggota_rombel_id', $request->anggota_rombel_id);
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('nilai', function ($item) {
            $links = '-';
            return $links;
        })
        ->addColumn('detil', function ($item) {
            $links = '<a href="'.route('ujian.detil_hasil', ['id' => $item->exam_id]).'" class="btn btn-sm btn-block btn-warning">Detil</a>';
            return $links;
        })
        ->rawColumns(['nilai', 'detil'])
        ->make(true);
    }
    public function get_detil_hasil_ujian(Request $request){
        $query = User_question::with(['soal.correct', 'answer'])->where(function($query) use ($request){
            $query->where('user_exam_id', $request->route('id'));
            $query->where('anggota_rombel_id', $request->anggota_rombel_id);
        });
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('soal', function ($item) {
            $links = $item->soal->question;//Str::limit($item->question->question,200);
            return $links;
        })
        ->addColumn('jawaban', function ($item) {
            $jawaban_ke = ($item->answer) ? $item->answer->jawaban_ke : 0;
            $links = ($jawaban_ke) ? Helper::generateAlphabet($jawaban_ke - 1) : '-';
            return $links;
        })
        ->addColumn('kunci', function ($item) {
            $jawaban_ke = $item->soal->correct->jawaban_ke;
            $links = Helper::generateAlphabet($jawaban_ke - 1);
            return $links;
        })
        ->addColumn('status', function ($item) {
            $jawaban = ($item->answer) ? $item->answer->jawaban_ke : 0;
            $kunci = $item->soal->correct->jawaban_ke;
            if($jawaban == $kunci){
                $links = '<button class="btn btn-sm btn-success">Benar</button>';
            } else {
                $links = '<button class="btn btn-sm btn-danger">Salah</button>';
            }
            return $links;
        })
        ->rawColumns(['soal', 'jawaban', 'kunci', 'status'])
        ->make(true);
    }
    public function get_all_users($request){
        $you = auth()->user();
        $query = User::query();
        return DataTables::of($query)
        ->addColumn('view', function ($user) {
            $links = '<a href="'.url('/users/' . $user->user_id).'" class="btn btn-sm btn-block btn-primary">View</a>';
            return $links;
        })
        ->addColumn('edit', function ($user) {
            $links = '<a href="'.url('/users/' . $user->user_id).'/edit" class="btn btn-sm btn-block btn-warning">Edit</a>';
            return $links;
        })
        ->addColumn('delete', function ($user) use ($you) {
            $links = '-';
            if( $you->user_id !== $user->user_id ){
                $links = '<a href="'.url('/users/delete/' . $user->user_id).'" class="btn btn-sm btn-block btn-danger">Delete</a>';
            }
            return $links;
        })
        ->rawColumns(['view', 'edit', 'delete'])
        ->make(true);
    }
    public function get_all_server($request){
        $callback = function($q) use ($request){
            $q->where('sekolah_id', $request->sekolah_id);
        };
        $query = Server::whereHas('sekolah', $callback)->with(['rombongan_belajar.anggota_rombel' => $callback])->orderBy('id_server');
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('nama_rombel', function ($item) {
            $isUuid = Uuid::isValid($item->rombongan_belajar_id);
            return ($isUuid) ? $item->rombongan_belajar->nama : 'Semua Rombel';
        })
        ->addColumn('actions', function ($item) {
            $links = '<div class="text-center">';
            $links .= '<div class="btn-group">';
            $links .= '<button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>';
            $links .= '<div class="dropdown-menu dropdown-menu-right">';
            $links .= '<a class="dropdown-item toggle-modal" href="'.route('referensi.detil', ['query' => 'server','id' => $item->server_id]).'">Peserta</a>';
            $links .= '<a class="dropdown-item toggle-delete" href="'.route('referensi.hapus', ['query' => 'server','id' => $item->server_id]).'">Hapus</a>';
            $links .= '</div>';
            $links .= '</div>';
            $links .= '</div>';
            return $links;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
    public function get_all_ujian($request){
        $callback = function($q) use ($request){
            $q->where('sekolah_id', $request->sekolah_id);
            if($request->rombongan_belajar_id){
                $q->where('rombongan_belajar_id', $request->rombongan_belajar_id);
            }
        };
        $query = Exam::whereHas('pembelajaran', $callback)->with(['pembelajaran' => $callback])->orderBy('mata_pelajaran_id');
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('actions', function ($item) use ($request){
            if($request->rombongan_belajar_id){
                $links = '<a class="btn btn-sm btn-success" href="'.route('ujian.proses', ['ujian_id' => $item->exam_id]).'">Mulai Ujian</a>';
            } else {
                $links = '<div class="text-center">';
                $links .= '<div class="btn-group">';
                $links .= '<button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>';
                $links .= '<div class="dropdown-menu dropdown-menu-right">';
                $links .= '<a class="dropdown-item" href="'.route('ujian.proses', ['ujian_id' => $item->exam_id]).'">Uji Coba</a>';
                $links .= '<a class="dropdown-item toggle-modal" href="'.route('materi.detil', ['query' => 'ujian','id' => $item->exam_id]).'">Detil</a>';
                $links .= '<a class="dropdown-item" href="'.route('ujian.soal', ['ujian_id' => $item->exam_id]).'">Atur Soal</a>';
                $links .= '<a class="dropdown-item toggle-delete" href="'.route('materi.hapus', ['query' => 'ujian','id' => $item->exam_id]).'">Hapus</a>';
                $links .= '</div>';
                $links .= '</div>';
                $links .= '</div>';
            }
            return $links;
        })
        ->rawColumns(['nama_mata_pelajaran', 'soal', 'actions'])
        ->make(true);
    }
    public function get_all_mata_pelajaran_kurikulum($request){
        $query = Mata_pelajaran_kurikulum::whereNull('deleted_at')->with(['mata_pelajaran', 'kurikulum', 'tingkat_pendidikan'])->orderBy('kurikulum_id')->orderBy('mata_pelajaran_id')->orderBy('tingkat_pendidikan_id');
        return DataTables::of($query)->make(true);
    }
    public function get_all_soal(Request $request){
        $query = Question::where(function($query) use ($request){
            $query->where('exam_id', $request->route('ujian_id'));
        })->orderBy('soal_ke');
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('actions', function ($item) {
            $links = '<div class="text-center">';
            $links .= '<div class="btn-group">';
            $links .= '<button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>';
            $links .= '<div class="dropdown-menu dropdown-menu-right">';
            $links .= '<a class="dropdown-item toggle-modal" href="'.route('materi.detil', ['query' => 'question','id' => $item->question_id]).'">Detil</a>';
            $links .= '<a class="dropdown-item toggle-delete" href="'.route('materi.hapus', ['query' => 'question','id' => $item->question_id]).'">Hapus</a>';
            $links .= '</div>';
            $links .= '</div>';
            $links .= '</div>';
            return $links;
            return $links;

        })
        ->rawColumns(['question', 'actions'])
        ->make(true);
    }
    public function get_all_bank_soal($request){
        $query = Bank_soal::with('mata_pelajaran')->where(function($query) use ($request){
            if($request->sekolah_id){
                $query->whereHas('ptk', function($sq) use ($request) {
                    $sq->where('sekolah_id', $request->sekolah_id);
                });
            }
            if($request->mata_pelajaran_id){
                $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
                /*$query->whereNotIn('bank_soal_id',function($sq){
                    $sq->select('bank_soal_id')->from('invite_users');
                });*/
                $query->doesnthave('question');
            }
        })->orderBy('soal_ke')->orderBy('mata_pelajaran_id')->orderBy('ptk_id');
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('nama_mata_pelajaran', function ($item) {
            $links = $item->mata_pelajaran->nama;
            return $links;

        })
        ->addColumn('soal', function ($item) {
            $links = Str::limit($item->soal,200);
            return $links;

        })
        ->addColumn('actions', function ($item){
            $links = '<div class="text-center">';
            $links .= '<div class="btn-group">';
            $links .= '<button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>';
            $links .= '<div class="dropdown-menu dropdown-menu-right">';
            $links .= '<a class="dropdown-item toggle-modal" href="'.route('materi.detil', ['query' => 'bank-soal','id' => $item->bank_soal_id]).'">Detil</a>';
            $links .= '<a class="dropdown-item toggle-delete" href="'.route('materi.hapus', ['query' => 'bank-soal','id' => $item->bank_soal_id]).'">Hapus</a>';
            $links .= '</div>';
            $links .= '</div>';
            $links .= '</div>';
            return $links;
            return $links;

        })
        ->addColumn('insert', function ($item) use ($request) {
            $links = '';
            if($request->ujian_id){
                $links = '<div class="text-center">';
                $links .= '<a class="btn btn-sm btn-warning toggle-insert" href="'.route('ujian.insert_soal', ['ujian_id' => $request->ujian_id, 'id' => $item->bank_soal_id]).'">Tambahkan</a>';
                $links .= '</div>';
            }
            return $links;

        })
        ->rawColumns(['nama_mata_pelajaran', 'soal', 'actions', 'insert'])
        ->make(true);
    }
    public function get_all_sekolah($request){
        $query = Sekolah::orderBy('kabupaten')->orderBy('status_sekolah')->orderBy('nama');
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('nomor', function ($item) {
            $links = '<div class="text-center">-</div>';
            return $links;

        })
        ->addColumn('status', function ($item) {
            $links = ($item->lisensi) ? '<div class="text-center btn btn-sm btn-success">Aktif</div>' : '<div class="text-center btn btn-sm btn-danger">Non Aktif</div>';
            return $links;

        })
        ->addColumn('actions', function ($item) {
            $links = '<div class="text-center">';
            $links .= '<div class="btn-group">';
            $links .= '<button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>';
            $links .= '<div class="dropdown-menu dropdown-menu-right">';
            $links .= '<a class="dropdown-item toggle-modal" href="'.route('referensi.detil', ['query' => 'sekolah','id' => $item->sekolah_id]).'">Detil</a>';
            if(!$item->lisensi){
                $links .= '<a class="dropdown-item toggle-swal" href="'.route('referensi.lisensi', ['id' => $item->sekolah_id]).'">Lisensi</a>';
            }
            $links .= '<a class="dropdown-item toggle-delete" href="'.route('referensi.hapus', ['query' => 'sekolah','id' => $item->sekolah_id]).'">Hapus</a>';
            $links .= '</div>';
            $links .= '</div>';
            $links .= '</div>';
            return $links;
            return $links;

        })
        ->rawColumns(['nomor', 'nama_kabupaten', 'nama_provinsi', 'status', 'actions'])
        ->make(true);
    }
    public function get_all_ptk($request){
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->with('peserta.sekolah')->first();
        $sekolah_id = [];
        if($event){
            foreach($event->peserta as $peserta){
                $sekolah_id[] = $peserta->sekolah->sekolah_id;
            }
            $query = Ptk::with('user')->where(function($query) use ($sekolah_id){
                $query->whereIn('sekolah_id', $sekolah_id);
            })->orderBy('sekolah_id')->orderBy('nama');
        } else {
            $server = Server::where('id_server', $user->username)->first();
            if($server){
                $query = Ptk::with('user')->where(function($query) use ($server){
                     $query->where('sekolah_id', $server->sekolah_id);
                })->orderBy('sekolah_id')->orderBy('nama');
            } else {
                $query = Ptk::with('user')->where(function($query) use ($server){
                    $query->whereNull('sekolah_id');
               })->orderBy('sekolah_id')->orderBy('nama');
            }
        }
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('sekolah', function ($item) {
            $links = $item->sekolah->nama;
            return $links;

        })
        ->addColumn('password', function ($item) {
            $links = (Hash::check($item->user->default_password, $item->user->password)) ? $item->user->default_password : '<a class="btn btn-danger btn-sm toggle-reset" title="Klik untuk mengatur ulang password" href="'.route('referensi.hapus', ['query' => 'reset-password','id' => $item->user->user_id]).'">Custom</a>';
            return $links;
        })
        ->addColumn('actions', function ($item) {
            $links = '<div class="text-center">';
            $links .= '<div class="btn-group">';
            $links .= '<button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>';
            $links .= '<div class="dropdown-menu dropdown-menu-right">';
            $links .= '<a class="dropdown-item toggle-modal" href="'.route('referensi.detil', ['query' => 'ptk','id' => $item->ptk_id]).'">Detil</a>';
            $links .= '<a class="dropdown-item toggle-delete" href="'.route('referensi.hapus', ['query' => 'ptk','id' => $item->ptk_id]).'">Hapus</a>';
            $links .= '</div>';
            $links .= '</div>';
            $links .= '</div>';
            return $links;
            return $links;

        })
        ->rawColumns(['password', 'actions'])
        ->make(true);
    }
    public function get_all_peserta_didik($request){
        $user = auth()->user();
        $event = Event::where('kode', $user->username)->first();
        $callback = function($query) use ($request){
            $query->with('rombongan_belajar');
            $query->where('semester_id', config('global.semester_id'));
        };
        $query_datatables = Peserta_didik::query()->whereHas('anggota_rombel', $callback)->with(['user','anggota_rombel' => $callback])->where(function($query) use ($request, $event){
            if($request->sekolah_id){
                $query->where('sekolah_id', $request->sekolah_id);
            } else {
                $query->whereIn('sekolah_id', function($query) use ($event){
                    $query->select('sekolah_id')->from('peserta_events')->where('event_id', $event->id);
                });
            }
            if($request->tingkat_pendidikan_id){
                $query->whereIn('peserta_didik_id', function($query) use ($request, $event){
                    $query->select('peserta_didik_id')->from('anggota_rombel')->whereIn('rombongan_belajar_id', function($query) use ($request, $event){
                        if($event){
                            $query->select('rombongan_belajar_id')->from('rombongan_belajar')->where('tingkat', $request->tingkat_pendidikan_id)->whereIn('sekolah_id', function($query) use ($event){
                                $query->select('sekolah_id')->from('peserta_events')->where('event_id', $event->id);
                            });
                        } else {
                            $query->select('rombongan_belajar_id')->from('rombongan_belajar')->where('tingkat', $request->tingkat_pendidikan_id)->where('sekolah_id', $request->sekolah_id);
                        }
                    });
                });
            }
            if($request->rombongan_belajar_id){
                $query->whereIn('peserta_didik_id', function($query) use ($request){
                    $query->select('peserta_didik_id')->from('anggota_rombel')->where('rombongan_belajar_id', $request->rombongan_belajar_id);
                });
            }
        })->orderBy('sekolah_id')->orderBy('nama');
        return DataTables::of($query_datatables)
        ->addIndexColumn()
        ->addColumn('sekolah', function ($item) {
            $links = $item->sekolah->nama;
            return $links;

        })
        ->addColumn('rombongan_belajar', function ($item) use ($request, $event){
            if($request->tingkat_pendidikan_id){
                $all_data = Rombongan_belajar::with('sekolah')->where(function($query) use ($request, $event){
                    if($event){
                        $query->whereIn('sekolah_id', function($query) use ($event){
                            $query->select('sekolah_id')->from('peserta_events')->where('event_id', $event->id);
                        });
                    } else {
                        $query->where('sekolah_id', $request->sekolah_id);
                    }
                    $query->where('tingkat', $request->tingkat_pendidikan_id);
                })->get();
                if($all_data->count()){
                    foreach($all_data as $rombel){
                        $record= array();
                        $record['id'] 	= $rombel->rombongan_belajar_id;
                        if($event){
                            $record['text'] 	= $rombel->nama. ' | '.$rombel->sekolah->nama;
                        } else {
                            $record['text'] 	= $rombel->nama;
                        }
                        $output['result'][] = $record;
                    }
                } else {
                    $record['id'] 	= '';
                    $record['text'] 	= 'Tidak ditemukan data rombongan belajar';
                    $output['result'][] = $record;
                }
                $output['query'] = TRUE;
            } else {
                $output['query'] = FALSE;
            }
            if($request->rombongan_belajar_id){
                $output['query'] = FALSE;
            }
            return $output;
        })
        ->addColumn('kelas', function ($item) {
            $links = ($item->anggota_rombel) ? $item->anggota_rombel->rombongan_belajar->nama : '-';
            return $links;

        })
        ->addColumn('j_k', function ($item) {
            $links = ($item->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan';
            return $links;
        })
        ->addColumn('password', function ($item) {
            $links = (Hash::check($item->user->default_password, $item->user->password)) ? $item->user->default_password : '<a class="btn btn-danger btn-sm toggle-reset" title="Klik untuk mengatur ulang password" href="'.route('referensi.hapus', ['query' => 'reset-password','id' => $item->user->user_id]).'">Custom</a>';
            return $links;
        })
        ->addColumn('actions', function ($item) {
            $links = '<div class="text-center">';
            $links .= '<div class="btn-group">';
            $links .= '<button class="btn btn-sm btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>';
            $links .= '<div class="dropdown-menu dropdown-menu-right">';
            $links .= '<a class="dropdown-item toggle-modal" href="'.route('referensi.detil', ['query' => 'peserta-didik','id' => $item->peserta_didik_id]).'">Detil</a>';
            $links .= '<a class="dropdown-item toggle-delete" href="'.route('referensi.hapus', ['query' => 'peserta-didik','id' => $item->peserta_didik_id]).'">Hapus</a>';
            $links .= '</div>';
            $links .= '</div>';
            $links .= '</div>';
            return $links;
            return $links;

        })
        ->rawColumns(['password', 'actions'])
        ->make(true);
    }
    public function get_all_rombongan_belajar($request){
        $query = Rombongan_belajar::where(function($query) use ($request){
            if($request->sekolah_id){
                $query->where('sekolah_id', $request->sekolah_id);
            }
            $query->where('semester_id', config('global.semester_id'));
        })->orderBy('sekolah_id')->orderBy('tingkat');
        return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('sekolah', function ($item) {
            $links = $item->sekolah->nama;
            return $links;

        })
        ->addColumn('anggota', function ($item) {
            $links = '<div class="text-center"><a class="btn btn-sm btn-primary toggle-modal" href="'.route('referensi.detil', ['query' => 'anggota-rombel','id' => $item->rombongan_belajar_id]).'">Anggota Rombel</a></div>';
            return $links;

        })
        ->addColumn('pembelajaran', function ($item) {
            if($item->kurikulum_id){
                $links = '<div class="text-center"><a class="btn btn-sm btn-info toggle-modal" href="'.route('referensi.detil', ['query' => 'pembelajaran','id' => $item->rombongan_belajar_id]).'">Pembelajaran</a></div>';
            } else {
                $links = '<div class="text-center"><a class="btn btn-sm btn-info toggle-select" href="'.route('referensi.detil', ['query' => 'kurikulum','id' => $item->rombongan_belajar_id]).'">Pilih Kurikulum</a></div>';
            }
            return $links;

        })
        ->addColumn('actions', function ($item) {
            $links = '<div class="text-center">';
            $links .= '<div class="btn-group">';
            $links .= '<button class="btn btn-sm btn-danger dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</button>';
            $links .= '<div class="dropdown-menu dropdown-menu-right">';
            $links .= '<a class="dropdown-item toggle-modal" href="'.route('referensi.detil', ['query' => 'rombongan-belajar','id' => $item->rombongan_belajar_id]).'">Detil</a>';
            $links .= '<a class="dropdown-item toggle-delete" href="'.route('referensi.hapus', ['query' => 'rombongan-belajar','id' => $item->rombongan_belajar_id]).'">Hapus</a>';
            $links .= '</div>';
            $links .= '</div>';
            $links .= '</div>';
            return $links;
            return $links;

        })
        ->rawColumns(['anggota', 'pembelajaran', 'actions'])
        ->make(true);
    }
    public function get_wilayah(Request $request){
        if($request->kecamatan_id){
            $all_wilayah = Mst_wilayah::where(DB::raw("TRIM(mst_kode_wilayah)"), trim($request->kecamatan_id))->get();
        } elseif($request->kabupaten_id){
            $all_wilayah = Mst_wilayah::where(DB::raw("TRIM(mst_kode_wilayah)"), trim($request->kabupaten_id))->get();
        } else{
            $all_wilayah = Mst_wilayah::where(DB::raw("TRIM(mst_kode_wilayah)"), trim($request->provinsi_id))->get();
        }
        if($all_wilayah->count()){
            foreach($all_wilayah as $wilayah){
                $record= array();
                $record['id'] 	= $wilayah->kode_wilayah;
                $record['text'] 	= $wilayah->nama;
                $output['results'][] = $record;
            }
        } else {
            $record['id'] 	= '';
			$record['text'] 	= 'Tidak ditemukan data wilayah';
			$output['results'][] = $record;
        }
        return response()->json($output);
    }
    public function get_data(Request $request){
        $query = $request->route('query');
        if($query == 'mata-pelajaran-kurikulum'){
            $all_kurikulum = Kurikulum::where('jurusan_id', $request->jurusan_id)->get();
            if($all_kurikulum->count()){
                foreach($all_kurikulum as $kurikulum){
                    $record= array();
                    $record['id'] 	= $kurikulum->kurikulum_id;
                    $record['text'] 	= $kurikulum->nama_kurikulum;
                    $output['results'][] = $record;
                }
            } else {
                $record['id'] 	= '';
                $record['text'] 	= 'Tidak ditemukan data kurikulum';
                $output['results'][] = $record;
            }
        } elseif($query == 'tingkat-pendidikan'){
            $kurikulum = Kurikulum::find($request->kurikulum_id);
            $all_tingkat = Tingkat_pendidikan::where('jenjang_pendidikan_id', $kurikulum->jenjang_pendidikan_id)->get();
            if($all_tingkat->count()){
                foreach($all_tingkat as $tingkat){
                    $record= array();
                    $record['id'] 	= $tingkat->tingkat_pendidikan_id;
                    $record['text'] 	= $tingkat->nama;
                    $output['results'][] = $record;
                }
            } else {
                $record['id'] 	= '';
                $record['text'] 	= 'Tidak ditemukan data tingkat pendidikan';
                $output['results'][] = $record;
            }
        } elseif($query == 'peserta-server'){
            $server_id = $request->server_id;
            $rombongan_belajar_id = $request->rombongan_belajar_id;
            $anggota_rombel = Anggota_rombel::where('rombongan_belajar_id', $rombongan_belajar_id)->where('server_id', $server_id)->orWhereNull('server_id')->where('rombongan_belajar_id', $rombongan_belajar_id)->get();
            return view('referensi.server.peserta-server', compact('server_id', 'rombongan_belajar_id', 'anggota_rombel'));
        } elseif($query == 'pembelajaran'){
            $all_data = Pembelajaran::where('rombongan_belajar_id', $request->rombongan_belajar_id)->get();
            if($all_data->count()){
                foreach($all_data as $data){
                    $record= array();
                    $record['id'] 	= $data->pembelajaran_id;
                    $record['text'] 	= $data->nama_mata_pelajaran;
                    $output['results'][] = $record;
                }
            } else {
                $record['id'] 	= '';
                $record['text'] 	= 'Tidak ditemukan mata pelajaran di rombel terpilih';
                $output['results'][] = $record;
            }
        } elseif($query == 'mata-ujian'){
            $mata_ujian = Exam::where('pembelajaran_id', $request->pembelajaran_id)->whereAktif(0)->get();
            if($mata_ujian->count()){
                foreach($mata_ujian as $exam){
                    $record= array();
                    $record['id'] 	= $exam->exam_id;
                    $record['text'] 	= $exam->nama;
                    $output['results'][] = $record;
                }
            } else {
                $record['id'] 	= '';
                $record['text'] 	= 'Tidak ditemukan mata ujian di mata pelajaran terpilih';
                $output['results'][] = $record;
            }
        } elseif($query == 'mata-ujian-event'){
            $mata_ujian = Exam::where('ujian_id', $request->ujian_id)->whereAktif(0)->get();
            if($mata_ujian->count()){
                foreach($mata_ujian as $exam){
                    $record= array();
                    $record['id'] 	= $exam->exam_id;
                    $record['text'] 	= $exam->nama;
                    $output['results'][] = $record;
                }
            } else {
                $record['id'] 	= '';
                $record['text'] 	= 'Tidak ditemukan mata ujian di mata pelajaran terpilih';
                $output['results'][] = $record;
            }
        } else {
            $record['id'] 	= '';
			$record['text'] 	= 'Output '.$query.' belum tersedia';
			$output['results'][] = $record;
        }
        return response()->json($output);
    }
    public function get_all_mata_pelajaran($request){
        if ($request->has('q')) {
            $mata_pelajaran = Mata_pelajaran::where('nama', 'like', '%' . $request->q . '%')->get();
            if($mata_pelajaran->count()){
                foreach($mata_pelajaran as $mapel){
                    $record= array();
                    $record['id'] 	= $mapel->mata_pelajaran_id;
                    $record['text'] 	= $mapel->nama;
                    $output['results'][] = $record;
                }
            } else {
                $record['id'] 	= '';
                $record['text'] 	= 'Tidak ditemukan data Mata Pelajaran';
                $output['results'][] = $record;
            }
        } else {
            $record['id'] 	= '';
			$record['text'] 	= 'Ketik nama Mata Pelajaran';
            $output['results'][] = $record;
        }
        return response()->json($output);
    }
}
