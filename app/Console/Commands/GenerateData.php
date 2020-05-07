<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Faker\Factory as Faker;
use App\Mst_wilayah;
use App\Sekolah;
use App\Ptk;
use App\Peserta_didik;
use App\Rombongan_belajar;
use App\Anggota_rombel;
use App\User;
use App\Jurusan;
use App\Kurikulum;
use Str;
class GenerateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:data';

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
        $data_jurusan = [
            [
                'jurusan_id' => 'MI0000',
                'nama_jurusan' => 'MI 2006',
                'untuk_sma' => 1,
                'untuk_smk' => 0,
                'untuk_pt' => 0,
                'untuk_slb' => 0,
                'untuk_smklb' => 0,
                'jenjang_pendidikan_id' => 4,
                'level_bidang_id' => 'MI'
            ],
            [
                'jurusan_id' => 'MI0001',
                'nama_jurusan' => 'MI 2006 (Kombinasi)',
                'untuk_sma' => 1,
                'untuk_smk' => 0,
                'untuk_pt' => 0,
                'untuk_slb' => 0,
                'untuk_smklb' => 0,
                'jenjang_pendidikan_id' => 4,
                'level_bidang_id' => 'MA'
            ],
            [
                'jurusan_id' => 'MI0002',
                'nama_jurusan' => 'MI 2013',
                'untuk_sma' => 1,
                'untuk_smk' => 0,
                'untuk_pt' => 0,
                'untuk_slb' => 0,
                'untuk_smklb' => 0,
                'jenjang_pendidikan_id' => 4,
                'level_bidang_id' => 'MA'
            ],
            [
                'jurusan_id' => 'MTS0000',
                'nama_jurusan' => 'MTs 2006',
                'untuk_sma' => 1,
                'untuk_smk' => 0,
                'untuk_pt' => 0,
                'untuk_slb' => 0,
                'untuk_smklb' => 0,
                'jenjang_pendidikan_id' => 5,
                'level_bidang_id' => 'MTs'
            ],
            [
                'jurusan_id' => 'MTS0001',
                'nama_jurusan' => 'MTs 2006 (Kombinasi)',
                'untuk_sma' => 1,
                'untuk_smk' => 0,
                'untuk_pt' => 0,
                'untuk_slb' => 0,
                'untuk_smklb' => 0,
                'jenjang_pendidikan_id' => 5,
                'level_bidang_id' => 'MTs'
            ],
            [
                'jurusan_id' => 'MTS0002',
                'nama_jurusan' => 'MTs 2013',
                'untuk_sma' => 1,
                'untuk_smk' => 0,
                'untuk_pt' => 0,
                'untuk_slb' => 0,
                'untuk_smklb' => 0,
                'jenjang_pendidikan_id' => 5,
                'level_bidang_id' => 'MTs'
            ],
            [
                'jurusan_id' => 'MA0000',
                'nama_jurusan' => 'MA 2006',
                'untuk_sma' => 1,
                'untuk_smk' => 0,
                'untuk_pt' => 0,
                'untuk_slb' => 0,
                'untuk_smklb' => 0,
                'jenjang_pendidikan_id' => 6,
                'level_bidang_id' => 'MA'
            ],
            [
                'jurusan_id' => 'MA0001',
                'nama_jurusan' => 'MA 2006 (Kombinasi)',
                'untuk_sma' => 1,
                'untuk_smk' => 0,
                'untuk_pt' => 0,
                'untuk_slb' => 0,
                'untuk_smklb' => 0,
                'jenjang_pendidikan_id' => 6,
                'level_bidang_id' => 'MA'
            ],
            [
                'jurusan_id' => 'MA0002',
                'nama_jurusan' => 'MA 2013',
                'untuk_sma' => 1,
                'untuk_smk' => 0,
                'untuk_pt' => 0,
                'untuk_slb' => 0,
                'untuk_smklb' => 0,
                'jenjang_pendidikan_id' => 6,
                'level_bidang_id' => 'MA'
            ]
        ];
        foreach($data_jurusan as $jurusan){
            Jurusan::create([
                'jurusan_id' 			=> $jurusan['jurusan_id'],
                'nama_jurusan' 			=> $jurusan['nama_jurusan'],
                'untuk_sma'				=> $jurusan['untuk_sma'],
                'untuk_smk'				=> $jurusan['untuk_smk'],
                'untuk_pt'				=> $jurusan['untuk_pt'],
                'untuk_slb'				=> $jurusan['untuk_slb'],
                'untuk_smklb'			=> $jurusan['untuk_smklb'],
                'jenjang_pendidikan_id'	=> $jurusan['jenjang_pendidikan_id'],
                'level_bidang_id'		=> $jurusan['level_bidang_id'],
            ]);
        }
        $data_kurikulum = [
            [
                'nama_kurikulum' 		=> 'MI 2006 Umum',
                'mulai_berlaku'			=> date('Y-m-d'),
                'sistem_sks'			=> 0,
                'total_sks'				=> 0,
                'jenjang_pendidikan_id'	=> 4,
                'jurusan_id'			=> 'MI0000'
            ],
            [
                'nama_kurikulum' 		=> 'MI 2006 Kombinasi',
                'mulai_berlaku'			=> date('Y-m-d'),
                'sistem_sks'			=> 0,
                'total_sks'				=> 0,
                'jenjang_pendidikan_id'	=> 4,
                'jurusan_id'			=> 'MI0001'
            ],
            [
                'nama_kurikulum' 		=> 'MI 2013',
                'mulai_berlaku'			=> date('Y-m-d'),
                'sistem_sks'			=> 0,
                'total_sks'				=> 0,
                'jenjang_pendidikan_id'	=> 4,
                'jurusan_id'			=> 'MI0002'
            ],
            [
                'nama_kurikulum' 		=> 'MTs 2006 Umum',
                'mulai_berlaku'			=> date('Y-m-d'),
                'sistem_sks'			=> 0,
                'total_sks'				=> 0,
                'jenjang_pendidikan_id'	=> 5,
                'jurusan_id'			=> 'MTS0000'
            ],
            [
                'nama_kurikulum' 		=> 'MTs 2006 Kombinasi',
                'mulai_berlaku'			=> date('Y-m-d'),
                'sistem_sks'			=> 0,
                'total_sks'				=> 0,
                'jenjang_pendidikan_id'	=> 5,
                'jurusan_id'			=> 'MTS0001'
            ],
            [
                'nama_kurikulum' 		=> 'MTs 2013',
                'mulai_berlaku'			=> date('Y-m-d'),
                'sistem_sks'			=> 0,
                'total_sks'				=> 0,
                'jenjang_pendidikan_id'	=> 5,
                'jurusan_id'			=> 'MTS0002'
            ],
            [
                'nama_kurikulum' 		=> 'MA 2006 Umum',
                'mulai_berlaku'			=> date('Y-m-d'),
                'sistem_sks'			=> 0,
                'total_sks'				=> 0,
                'jenjang_pendidikan_id'	=> 6,
                'jurusan_id'			=> 'MA0000'
            ],
            [
                'nama_kurikulum' 		=> 'MA 2006 Kombinasi',
                'mulai_berlaku'			=> date('Y-m-d'),
                'sistem_sks'			=> 0,
                'total_sks'				=> 0,
                'jenjang_pendidikan_id'	=> 6,
                'jurusan_id'			=> 'MA0001'
            ],
            [
                'nama_kurikulum' 		=> 'MA 2013',
                'mulai_berlaku'			=> date('Y-m-d'),
                'sistem_sks'			=> 0,
                'total_sks'				=> 0,
                'jenjang_pendidikan_id'	=> 6,
                'jurusan_id'			=> 'MA0002'
            ]
        ];
        foreach($data_kurikulum as $kurikulum){
            $get_kurikulum = Kurikulum::orderBy('kurikulum_id', 'desc')->first();
            Kurikulum::create([
                'kurikulum_id' 			=> ($get_kurikulum->kurikulum_id + 1),
                'nama_kurikulum' 		=> $kurikulum['nama_kurikulum'],
                'mulai_berlaku'			=> $kurikulum['mulai_berlaku'],
                'sistem_sks'			=> $kurikulum['sistem_sks'],
                'total_sks'				=> $kurikulum['total_sks'],
                'jenjang_pendidikan_id'	=> $kurikulum['jenjang_pendidikan_id'],
                'jurusan_id'			=> $kurikulum['jurusan_id'],
            ]);
        }
        /*
        $file = storage_path('app/public/uploads/data_sekolah.xlsx');
        $data_upload = (new FastExcel)->import($file);
        foreach($data_upload as $data){
            $npsn = $data['npsn'];
            unset($data['No'],$data['npsn']);
            $wilayah = Mst_wilayah::with('parent')->find($data['kode_wilayah']);
            $data['sekolah_id']        = Str::uuid();
            $data['desa_kelurahan']    = $wilayah->nama;
            $data['kecamatan']         = $wilayah->parent->nama;
            $data['kabupaten']         = $wilayah->parent->parent->nama;
            $data['provinsi']          = $wilayah->parent->parent->parent->nama;
            Sekolah::updateOrCreate(
                [
                    'npsn' => $npsn
                ],
                $data
            );
        }
        $numberOfUsers = 10;
        $data_sekolah = Sekolah::get();
        $faker = Faker::create();
        $usersIds = [];
        $sekolah = Sekolah::where('npsn', '20605058')->first();
        //foreach($data_sekolah as $sekolah){
            //create ptk
            $user = User::create([ 
                'name' => $sekolah->nama,
                'sekolah_id' => $sekolah->sekolah_id,
                'username' => strtolower(str_replace(' ', '', $sekolah->nama)),
                'email' => $sekolah->email,
                'email_verified_at' => now(),
                'password' => app('hash')->make('12345678'),
                'remember_token' => Str::random(10),
                'menuroles' => 'sekolah'
            ]);
            $user->assignRole('sekolah');
            array_push($usersIds, $user->id);
            for($i = 0; $i<$numberOfUsers; $i++){
                $ptk = Ptk::create([
                    'ptk_id' => Str::uuid(),
                    'sekolah_id' => $sekolah->sekolah_id,
                    'nama' => $faker->unique()->name(),
                    'nuptk' => $faker->randomNumber(),
                    'jenis_kelamin' => 'L',
                    'tempat_lahir' => $faker->name(),
                    'tanggal_lahir' => date('Y-m-d'),
                    'agama_id' => 1,
                    'email' => $faker->unique()->safeEmail()
                ]);
                $user = User::create([ 
                    'name' => $ptk->nama,
                    'sekolah_id' => $sekolah->sekolah_id,
                    'username' => strtolower(str_replace(' ', '', $ptk->nama)),
                    'email' => $ptk->email,
                    'email_verified_at' => now(),
                    'password' => app('hash')->make('12345678'),
                    'remember_token' => Str::random(10),
                    'ptk_id' => $ptk->ptk_id,
                    'menuroles' => 'ptk'
                ]);
                $user->assignRole('ptk');
                array_push($usersIds, $user->id);
            }
            $data_ptk = Ptk::where('sekolah_id', $sekolah->sekolah_id)->limit(3)->get();
            $a=10;
            foreach($data_ptk as $dptk){
                $rombongan_belajar = Rombongan_belajar::create([
                    'rombongan_belajar_id'  => Str::uuid(),
                    'sekolah_id' => $sekolah->sekolah_id,
                    'tingkat_pendidikan_id' => $a,
                    'semester_id' => 20201,
                    'kurikulum_id' => 24,
                    'nama' => 'Kelas '.$a,
                    'ptk_id' => $dptk->ptk_id
                ]);
                $a++;
                for($i = 0; $i<$numberOfUsers; $i++){
                    $peserta_didik = Peserta_didik::create([
                        'peserta_didik_id' => Str::uuid(),
                        'sekolah_id' => $sekolah->sekolah_id,
                        'nama' => $faker->unique()->name(),
                        'no_induk' => $faker->randomNumber(),
                        'nisn' => $faker->randomNumber(),
                        'jenis_kelamin' => 'L',
                        'tempat_lahir' => $faker->name(),
                        'tanggal_lahir' => $faker->date(),
                        'agama_id' => 1,
                        'email' => $faker->unique()->safeEmail(),
                    ]);
                    $user = User::create([ 
                        'name' => $peserta_didik->nama,
                        'sekolah_id' => $sekolah->sekolah_id,
                        'username' => strtolower(str_replace(' ', '', $peserta_didik->nama)),
                        'email' => $peserta_didik->email,
                        'email_verified_at' => now(),
                        'password' => app('hash')->make('12345678'),
                        'remember_token' => Str::random(10),
                        'peserta_didik_id' => $peserta_didik->peserta_didik_id,
                        'menuroles' => 'peserta_didik'
                    ]);
                    $user->assignRole('peserta_didik');
                    array_push($usersIds, $user->id);
                    Anggota_rombel::create([
                        'anggota_rombel_id' => Str::uuid(),
                        'sekolah_id' => $sekolah->sekolah_id,
                        'semester_id' => 20201,
                        'rombongan_belajar_id' => $rombongan_belajar->rombongan_belajar_id,
                        'peserta_didik_id' => $peserta_didik->peserta_didik_id,
                        'nama_peserta_didik' => $peserta_didik->nama
                    ]);
                }
            }
            $this->info($sekolah->nama);
        //}
        */
    }
}
