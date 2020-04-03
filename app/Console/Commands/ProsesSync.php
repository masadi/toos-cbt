<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
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
use Faker\Factory as Faker;
use Str;
use Helper;
class ProsesSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proses:sync {query} {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simpan data';

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
        $query = $this->argument('query');
        $data = $this->argument('data');
        if($query == 'download'){
            $data =  (object) $data;
            if($data->query == 'ptk'){
                if(isset($data->response['data']) && count($data->response['data'])){
                    foreach($data->response['data'] as $item){
                        $item = json_decode(json_encode($item));
                        $this->insert_ptk($item);
                    }
                }
            } elseif($data->query == 'rombongan_belajar'){
                if($data->response['data'] && is_array($data->response['data'])){
                    foreach($data->response['data'] as $item){
                        $item = json_decode(json_encode($item));
                        $this->insert_ptk($item->ptk);
                        $this->insert_jurusan_sp($item->jurusan_sp);
                        $this->insert_rombel($item);
                    }
                } else {
                    for($i=0;$i<=3;$i++){
                        sleep(1);
                    }
                }
            } elseif($data->query == 'pembelajaran'){
                if(isset($data->response['data']) && count($data->response['data'])){
                    foreach($data->response['data'] as $item){
                        $item = json_decode(json_encode($item));
                        $this->insert_ptk($item->ptk);
                        $this->insert_rombel($item->rombongan_belajar);
                        $this->insert_pembelajaran($item);
                    }
                }
            } elseif($data->query == 'anggota_rombel'){
                if(isset($data->response['data']) && count($data->response['data'])){
                    foreach($data->response['data'] as $item){
                        $item = json_decode(json_encode($item));
                        $this->insert_peserta_didik($item->peserta_didik);
                        $this->insert_anggota_rombel($item);
                    }
                }
            } elseif($data->query == 'exams'){
                if(isset($data->response['data']) && count($data->response['data'])){
                    foreach($data->response['data'] as $item){
                        $item = json_decode(json_encode($item));
                        $this->insert_exam($item);
                    }
                }
            } elseif($data->query == 'questions'){
                if(isset($data->response['data']) && count($data->response['data'])){
                    foreach($data->response['data'] as $item){
                        $item = json_decode(json_encode($item));
                        $this->insert_question($item);
                    }
                }
            } elseif($data->query == 'answers'){
                if(isset($data->response['data']) && count($data->response['data'])){
                    foreach($data->response['data'] as $item){
                        $item = json_decode(json_encode($item));
                        $this->insert_answer($item);
                    }
                }
            }
        } elseif($query == 'upload'){
            //$asd = json_decode(json_encode($data));
            //dd($data);
            //foreach($data['anggota_rombel_id'] as $key => $value){
                //echo $key.'=>'.$value;
            //}
            $host_server = config('global.url_server').'upload-ujian';
            /*$arguments = [
                'ptk_id' => $data['ptk_id'],
                'anggota_rombel' => $data['anggota_rombel_id'],
                'user_exams' => $data['user_exam_id'],
                'exam_id' => $data['exam_id'],
                'user_questions' => User_question::whereIn('user_exam_id', $data['user_exam_id'])->get()->toArray(),
            ];*/
            $arguments = [
                'data' => $data->toArray()
            ];
            $client = new Client(); //GuzzleHttp\Client
            $curl = $client->post($host_server, [	
                'form_params' => $arguments
            ]);
            if($curl->getStatusCode() == 200){
                $response = json_decode($curl->getBody());
                if($response->success){
                    //$update = User_exam::whereIn('user_exam_id', $data['user_exam_id'])->update(['status_upload' => 1]);
                    foreach($data as $d){
                        $d->status_upload = 1;
                        $d->save();
                    }
                    /*if($update){
                        //$this->info(1);
                        $output = [
                            'success' => FALSE,
                            'message' => 'Anggota Rombel di Portal terdeteksi sudah dihapus. Silahkan ekstrak ulang VDI!',
                        ];
                    } else {
                        //$this->info(0);
                        $output = [
                            'success' => FALSE,
                            'message' => 'Anggota Rombel di Portal terdeteksi sudah dihapus. Silahkan ekstrak ulang VDI!',
                        ];
                    }*/
                /*} else {
                    //$this->info(0);
                    $output = [
                        'success' => FALSE,
                        'message' => 'Anggota Rombel di Portal terdeteksi sudah dihapus. Silahkan ekstrak ulang VDI!',
                    ];*/
                }
                echo $curl->getBody();
            } else {
                //$this->info(0);
                $output = [
                    'icon' => 'error',
                    'success' => FALSE,
                    'message' => 'Server tidak merespon. Silahkan coba lagi',
                ];
                echo json_encode($output);
            }
        }
    }
    private function insert_ptk($item){
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
        $user = User::updateOrCreate(
            [
                'ptk_id' => $item->ptk_id
            ],
            [ 
                'name' => $item->nama,
                'sekolah_id' => $item->sekolah_id,
                'username' => $item->nuptk,
                'email' => ($item->email) ? $item->email : $faker->unique()->name(),
                'email_verified_at' => now(),
                'password' => app('hash')->make('12345678'),
                'timezone' => config('app.timezone'),
                'remember_token' => Str::random(10),
                'menuroles' => 'ptk'
            ]
        );
        $user->assignRole('ptk');
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
                'tingkat_pendidikan_id' => $item->tingkat_pendidikan_id,
                'jurusan_sp_id' => $item->jurusan_sp_id,
                'semester_id' => $item->semester_id,
                'jurusan_id' => $item->jurusan_id,
                'kurikulum_id' => $item->kurikulum_id,
                'nama' => $item->nama,
                'ptk_id' => $item->ptk_id,
                'server_id' => $item->server_id
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
    private function insert_peserta_didik($item){
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
        $user = User::updateOrCreate(
            [
                'peserta_didik_id' => $item->peserta_didik_id
            ],
            [ 
                'name' => $item->nama,
                'sekolah_id' => $item->sekolah_id,
                'username' => $item->nisn,
                'email' => ($item->email) ? $item->email : $faker->unique()->name(),
                'email_verified_at' => now(),
                'password' => app('hash')->make('12345678'),
                'timezone' => config('app.timezone'),
                'remember_token' => Str::random(10),
                'menuroles' => 'peserta_didik'
            ]
        );
        $user->assignRole('peserta_didik');
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
                'durasi' => $item->durasi
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
