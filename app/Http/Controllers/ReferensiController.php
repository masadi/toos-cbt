<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Bentuk_pendidikan;
use App\Mst_wilayah;
use App\Sekolah;
use App\Ptk;
use App\Rombongan_belajar;
use App\Peserta_didik;
use App\Anggota_rombel;
use App\Jurusan;
use App\Mata_pelajaran_kurikulum;
use App\Pembelajaran;
use App\Server;
use App\User;
use App\Jadwal;
use App\Event;
use Helper;
use Validator;
use File;
class ReferensiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->path = storage_path('app/public/uploads');
    }
    public function index(Request $request){
        $user = auth()->user();
        $query = $request->route('query');
        if($query == 'sekolah'){
            return $this->sekolah($user);
        } elseif($query == 'ptk'){
            return $this->ptk($user);
        } elseif($query == 'peserta-didik'){
            return $this->peserta_didik($user);
        } elseif($query == 'rombongan-belajar'){
            return $this->rombongan_belajar($user);
        } elseif($query == 'mata-pelajaran-kurikulum'){
            return $this->mata_pelajaran_kurikulum($user);
        } elseif($query == 'server'){
            return $this->data_server($user);
        } else {
            echo 'fungsi '.$query.' belum ada!';
        }
    }
    public function data_server($user){
        return view('referensi.server.index', compact('user'));
    }
    public function sekolah($user){
        if($user->hasRole('admin')){
            return view('referensi.sekolah.index', compact('user'));
        } else {
            $data = Sekolah::find($user->sekolah_id);
            return view('referensi.sekolah.detil-sekolah', compact('user', 'data'));
        }
    }
    public function ptk($user){
        return view('referensi.ptk.index', compact('user'));
    }
    public function peserta_didik($user){
        return view('referensi.peserta-didik.index', compact('user'));
    }
    public function rombongan_belajar($user){
        return view('referensi.rombongan-belajar.index', compact('user'));
    }
    public function mata_pelajaran_kurikulum($user){
        return view('referensi.mata-pelajaran-kurikulum.index', compact('user'));
    }
    public function tambah_data(Request $request){
        $query = $request->route('query');
        if($query == 'sekolah'){
            return $this->tambah_sekolah();
        } elseif($query == 'ptk'){
            return $this->tambah_ptk();
        } elseif($query == 'peserta-didik'){
            return $this->tambah_peserta_didik();
        } elseif($query == 'rombongan-belajar'){
            return $this->tambah_rombongan_belajar();
        } elseif($query == 'mata-pelajaran-kurikulum'){
            return $this->tambah_mata_pelajaran_kurikulum();
        } elseif($query == 'server'){
            return $this->tambah_server();
        } else {
            echo 'fungsi '.$query.' belum ada!';
        }
    }
    public function tambah_server(){
        $user = auth()->user();
        $sekolah = Sekolah::find($user->sekolah_id);
        $rombongan_belajar = Rombongan_belajar::where('sekolah_id', $user->sekolah_id)->get();
        return view('referensi.server.tambah', compact('user', 'rombongan_belajar', 'sekolah'));
    }
    public function tambah_sekolah(){
        $bentuk_pendidikan = Bentuk_pendidikan::get();
        $all_provinsi = Mst_wilayah::where('id_level_wilayah', 1)->get();
        return view('referensi.sekolah.tambah', compact('bentuk_pendidikan', 'all_provinsi'));
    }
    public function tambah_ptk(){
        return view('referensi.ptk.tambah');
    }
    public function tambah_peserta_didik(){
        return view('referensi.peserta-didik.tambah');
    }
    public function tambah_rombongan_belajar(){
        return view('referensi.rombongan-belajar.tambah');
    }
    public function tambah_mata_pelajaran_kurikulum(){
        $data_jurusan = Jurusan::whereNull('deleted_at')->get();
        return view('referensi.mata-pelajaran-kurikulum.tambah', compact('data_jurusan'));
    }
    public function simpan(Request $request){
        $query = $request->route('query');
        if($query == 'sekolah'){
            $messages = [
                'nama.required'	=> 'Nama Sekolah tidak boleh kosong',
                'npsn.required'	=> 'NPSN tidak boleh kosong',
                'npsn.unique'	=> 'NPSN sudah terdaftar',
                'bentuk_pendidikan_id.required'        => 'Bentuk Pendidikan tidak boleh kosong',
                'provinsi_id.required'        => 'Provinsi tidak boleh kosong',
                'kabupaten_id.required'         => 'Kabupaten tidak boleh kosong',
                'kecamatan_id.required'     => 'Kecamatan tidak boleh kosong',
                'desa_kelurahan_id.required'     => 'Desa/Kelurahan tidak boleh kosong',
                'email.required'        => 'Email tidak boleh kosong',
                'status_sekolah.required'        => 'Status Sekolah tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'nama'	=> 'required',
                'npsn'	=> 'required|unique:App\Sekolah,npsn',
                'bentuk_pendidikan_id'         => 'required',
                'provinsi_id'          => 'required',
                'kabupaten_id'      => 'required',
                'kecamatan_id'      => 'required',
                'desa_kelurahan_id'      => 'required',
                'email'      => 'required',
                'status_sekolah'      => 'required',
            ],
            $messages
            );
        } elseif($query == 'mata-pelajaran-kurikulum'){
            $messages = [
                'kurikulum_id.required'	=> 'Kurikulum tidak boleh kosong',
                'mata_pelajaran_id.required'	=> 'Mata Pelajaran tidak boleh kosong',
                'tingkat_pendidikan_id.required'        => 'Tingkat Pendidikan tidak boleh kosong',
            ];
            $validator = Validator::make(request()->all(), [
                'kurikulum_id'	=> 'required',
                'mata_pelajaran_id'         => 'required',
                'tingkat_pendidikan_id'          => 'required',
            ],
            $messages
            );
            Mata_pelajaran_kurikulum::updateOrCreate(
                [
                    'kurikulum_id' => $request->kurikulum_id,
                    'mata_pelajaran_id' => $request->mata_pelajaran_id,
                    'tingkat_pendidikan_id' => $request->tingkat_pendidikan_id
                ],
                [
                    'jumlah_jam' => 0,
                    'jumlah_jam_maksimum' => 0,
                    'wajib' => 0,
                    'sks' => 0,
                    'a_peminatan' => 0,
                    'area_kompetensi' => '*',
                    'last_sync' => date('Y-m-d H:i:s')
                ]
            );
        } elseif($query == 'server'){
            $messages = [
                'rombongan_belajar_id.required'	=> 'Rombongan Belajar tidak boleh kosong',
                'sekolah_id.required'	=> 'Data Sekolah tidak ditemukan',
            ];
            $validator = Validator::make(request()->all(), [
                'rombongan_belajar_id'	=> 'required',
                'sekolah_id'	=> 'required',
            ],
            $messages
            );
            $isUuid = Uuid::isValid($request->rombongan_belajar_id);
            $id_server = $request->npsn.'-'.strtoupper(Str::random(4));
            $password = strtoupper(Str::random(5));
            $server = Server::create(
                [
                    'sekolah_id' => $request->sekolah_id,
                    'rombongan_belajar_id' => ($isUuid) ? $request->rombongan_belajar_id : NULL,
                    'id_server' => $id_server,
                    'password' => $password,
                ]
            );
            if($isUuid){
                Anggota_rombel::where('rombongan_belajar_id', $request->rombongan_belajar_id)->update(['server_id' => $server->server_id]);
            }
        } elseif($query == 'peserta-server'){
            //Anggota_rombel::where('rombongan_belajar_id', $request->rombongan_belajar_id)->update(['server_id' => NULL]);
            Anggota_rombel::where(function($query) use ($request){
                $query->where('rombongan_belajar_id', $request->rombongan_belajar_id);
                $query->where('server_id', $request->server_id);
            })->update(['server_id' => NULL]);
            $insert = 0;
            if($request->status){
                $insert = Anggota_rombel::whereIn('anggota_rombel_id', $request->status)->update(['server_id' => $request->server_id]);
            }
            if($insert){
                $response = [
                    'status' => 'Peserta Server berhasil disimpan',
                    'success' => true,
                    'icon' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'Tidak ada data Peserta Server disimpan',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
            return response()->json($response);
        } elseif($query == 'pembelajaran'){
            $data_ptk = ($request->has('ptk_id')) ? array_filter($request->ptk_id) : NULL;
            $mata_pelajaran = $request->mata_pelajaran_id;
            $nama_mapel = $request->nama_mata_pelajaran;
            if($data_ptk){
                foreach($data_ptk as $key => $ptk){
                    Pembelajaran::updateOrCreate(
                        [
                            'sekolah_id' => $request->sekolah_id,
                            'semester_id' => $request->semester_id,
                            'rombongan_belajar_id' => $request->rombongan_belajar_id,
                            'mata_pelajaran_id' => $mata_pelajaran[$key]
                        ],
                        [
                            'ptk_id' => $ptk,
                            'nama_mata_pelajaran' => $nama_mapel[$key]
                        ]
                    );
                }
                $response = [
                    'status' => 'Pembelajaran Berhasil Disimpan',
                    'success' => true,
                    'icon' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'Tidak ada data Pembelajaran Disimpan',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
            return response()->json($response);
        } else {
            echo 'Simpan '.$query.' tidak ditemukan';
            dd($request->all());
        }
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            if($query == 'sekolah'){
                $wilayah = Mst_wilayah::with('parent')->find($request->desa_kelurahan_id);
                $request->request->add(
                    [
                        'sekolah_id'        => Str::uuid(),
                        'desa_kelurahan'    => $wilayah->nama,
                        'kecamatan'         => $wilayah->parent->nama,
                        'kabupaten'         => $wilayah->parent->parent->nama,
                        'provinsi'          => $wilayah->parent->parent->parent->nama,
                        'kode_wilayah'      => $request->desa_kelurahan_id,
                    ]
                ); //add request
                $update_data = $request->except(['_token', 'desa_kelurahan_id', 'kecamatan_id', 'kabupaten_id', 'provinsi_id']);
                //dd($update_data);
                Sekolah::create($update_data);
            }
        }
        return redirect('referensi/'.$query)->with(['success' => 'Data Berhasil Ditambahkan']);
    }
    public function saveBulk(Request $request){
        $this->validate($request, [
            'file' => 'required|mimes:xlsx' //PASTIKAN FORMAT FILE YANG DITERIMA ADALAH XLSX
        ]);
        //JIKA FILE-NYA ADA
        if ($request->hasFile('file')) {
            $user = auth()->user();
            $title = $request->route('query');
            $file = $request->file('file');
            $filename = time() . '-'.strtolower($title).'.' . $file->getClientOriginalExtension();
            if (!File::isDirectory($this->path)) {
                //MAKA FOLDER TERSEBUT AKAN DIBUAT
                File::makeDirectory($this->path);
            }
            $file->storeAs('public/uploads', $filename); //MAKA SIMPAN FILE TERSEBUT DI STORAGE/APP/PUBLIC/UPLOADS
            $file_excel = storage_path('app/public/uploads/' . $filename);
            $data_upload = (new FastExcel)->import($file_excel);
            if($request->route('query') == 'sekolah'){
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
                unlink($file_excel);
            } elseif($request->route('query') == 'ptk'){
                //import ptk
            } elseif($request->route('query') == 'peserta-didik'){
                //import peserta-didik
            } elseif($request->route('query') == 'rombongan-belajar'){
                //import rombel
            }
            //BUAT JADWAL UNTUK PROSES FILE TERSEBUT DENGAN MENGGUNAKAN JOB
            //ADAPUN PADA DISPATCH KITA MENGIRIMKAN DUA PARAMETER SEBAGAI INFORMASI
            //YAKNI KATEGORI ID DAN NAMA FILENYA YANG SUDAH DISIMPAN
            return redirect()->back()->with(['success' => 'Data '.$title.' berhasil ditambahkan']);
        }
    }
    public function hapus(Request $request){
        $query = $request->route('query');
        $id = $request->route('id');
        if($query == 'sekolah'){
            $delete = Sekolah::find($id);
            if($delete->delete()){
                $response = [
                    'status' => 'Data Sekolah berhasil dihapus',
                    'success' => true,
                    'icon' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'Data Sekolah gagal dihapus',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
        } elseif($query == 'ptk'){
            $delete = Ptk::find($id);
            if($delete->delete()){
                $response = [
                    'status' => 'Data PTK berhasil dihapus',
                    'success' => true,
                    'icon' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'Data PTK gagal dihapus',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
        } elseif($query == 'rombongan-belajar'){
            $delete = Rombongan_belajar::find($id);
            if($delete->delete()){
                $response = [
                    'status' => 'Data Rombongan Belajar berhasil dihapus',
                    'success' => true,
                    'icon' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'Data Rombongan Belajar gagal dihapus',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
        } elseif($query == 'peserta-didik'){
            $delete = Peserta_didik::find($id);
            if($delete->delete()){
                $response = [
                    'status' => 'Data Peserta Didik berhasil dihapus',
                    'success' => true,
                    'icon' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'Data Peserta Didik gagal dihapus',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
        } elseif($query == 'server'){
            $delete = Server::find($id);
            Anggota_rombel::where('server_id', $delete->server_id)->update(['server_id' => NULL]);
            if($delete->delete()){
                $response = [
                    'status' => 'Data Server berhasil dihapus',
                    'success' => true,
                    'icon' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'Data Server gagal dihapus',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
        } elseif($query == 'reset-password'){
            $delete = User::find($id);
            $delete->password = app('hash')->make($delete->default_password);
            if($delete->save()){
                $response = [
                    'status' => 'Password Pengguna berhasil diatur ulang',
                    'success' => true,
                    'icon' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'Password Pengguna gagal diatur ulang',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
        } elseif($query == 'jadwal'){
            $delete = Jadwal::find($id);
            if($delete->delete()){
                $response = [
                    'status' => 'Jadwal berhasil dihapus',
                    'success' => true,
                    'icon' => 'success'
                ];
            } else {
                $response = [
                    'status' => 'Jadwal gagal dihapus',
                    'success' => false,
                    'icon' => 'error'
                ];
            }
        } else {
            $response = [
                'status' => 'Query tidak dikenal',
                'success' => false,
                'icon' => 'error'
            ];
        }
        return response()->json($response);
    }
    public function detil(Request $request){
        $user = auth()->user();
        $query = $request->route('query');
        $id = $request->route('id');
        $title = 'Detil Data '.$query;
        $data = '';
        $data_satu = '';
        $data_dua = '';
        $modal_s = '';
        if($query == 'sekolah'){
            $data = Sekolah::find($id);
        } elseif($query == 'ptk'){
            $data = Ptk::find($id);
        } elseif($query == 'rombongan-belajar'){
            $data = Rombongan_belajar::find($id);
        } elseif($query == 'peserta-didik'){
            $data = Peserta_didik::find($id);
        } elseif($query == 'anggota-rombel'){
            $data = Anggota_rombel::where('rombongan_belajar_id', $id)->get();
        } elseif($query == 'pembelajaran'){
            $data_satu = Rombongan_belajar::find($id);
            $data = Mata_pelajaran_kurikulum::with(['pembelajaran.rombongan_belajar' => function($q)use ($data_satu){
                $q->where('kurikulum_id', $data_satu->kurikulum_id);
                $q->where('tingkat_pendidikan_id', $data_satu->tingkat_pendidikan_id);
                $q->where('rombongan_belajar_id', $data_satu->rombongan_belajar_id);
            }])->where(function($q) use ($data_satu){
                $q->whereNull('deleted_at');
                $q->where('kurikulum_id', $data_satu->kurikulum_id);
                $q->where('tingkat_pendidikan_id', $data_satu->tingkat_pendidikan_id);
            })->get();
            if($user->sekolah_id){
                $data_dua = Ptk::where('sekolah_id', $user->sekolah_id)->get();
            } else {
                $data_dua = Ptk::where('sekolah_id', $data_satu->sekolah_id)->get();
            }
        } elseif($query == 'server'){
            $data = Server::with('rombongan_belajar.anggota_rombel')->find($id);
            if(!Uuid::isValid($data->rombongan_belajar_id)){
                $data_satu = Rombongan_belajar::where('sekolah_id', $data->sekolah_id)->get();
            }
            $modal_s = 'modalku';
        } elseif($query == 'jadwal'){
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
            $jadwal = Jadwal::find($id);
            $rombongan_belajar = Rombongan_belajar::with('pembelajaran')->find($jadwal->rombongan_belajar_id);
            return view('referensi.jadwal.detil', compact('all_tingkat', 'rombongan_belajar', 'jadwal'));
        }
        return view('referensi.'.$query.'.detil', compact('data', 'title', 'data_satu', 'data_dua', 'modal_s'));
    }
    public function lisensi($id){
        $sekolah = Sekolah::find($id);
        $sekolah->lisensi = Str::random(10);
        if($sekolah->save()){
            $response = [
                'status' => 'Lisensi Berhasil digenerate',
                'success' => true,
                'icon' => 'success'
            ];
        } else {
            $response = [
                'status' => 'Lisensi Gagal digenerate',
                'success' => false,
                'icon' => 'error'
            ];
        }
        return response()->json($response);
    }
}
