<?php
use App\Setting;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/login-sekolah',      'LoginController@sekolah');
Route::get('/login-proktor',      'LoginController@proktor');
Route::post('/sekolah-login',      'LoginController@login_sekolah')->name('sekolah_login');
Route::post('/proktor-login',      'LoginController@login_proktor')->name('proktor_login');
Route::get('/add-domain', function () {
	Artisan::call('domain:add us.smkplus.sch.id');
});
Auth::routes();
Route::group(['middleware' => ['auth', 'get.menu']], function () {
    //Route::get('/', function () {           return view('dashboard.homepage'); });
    Route::get('/',      'HomeController@index');
    Route::get('/signout',      'HomeController@signout');
    Route::get('/users/profile','UsersController@profile')->name('users.profile');
    Route::post('/users/update-data/{id}','UsersController@update_data')->name('users.update_data');
    Route::prefix('ajax')->group(function () {
        Route::get('/get-all-{query}','AjaxController@get_all_data')->name('ajax.get_all_data');
        Route::get('/get-all-materi/{query}','AjaxController@get_all_materi')->name('ajax.get_all_materi');
        Route::get('/get-all-soal/{ujian_id}','AjaxController@get_all_soal')->name('ajax.get_all_soal');
        Route::get('/get-soal/{pembelajaran_id}','AjaxController@get_soal')->name('ajax.get_soal');
        Route::post('/get-wilayah','AjaxController@get_wilayah')->name('ajax.get_wilayah');
        //Route::get('/get-all-{query}','AjaxController@get_all_data')->name('ajax.get_all_data');
        Route::post('/get-data-{query}','AjaxController@get_data')->name('ajax.get_data');
        Route::get('/get-detil-hasil-ujian/{id}','AjaxController@get_detil_hasil_ujian')->name('ajax.get_detil_hasil_ujian');
    });
    Route::group(['middleware' => ['role:proktor']], function () {
        Route::get('/check-update', function (\Codedge\Updater\UpdaterManager $updater) {
            if($updater->source()->isNewVersionAvailable()) {
                if (version_compare($updater->source()->getVersionInstalled(), $updater->source()->getVersionAvailable(), '<')) {
                    $output['update'] = TRUE;
                } else {
                    $output['update'] = FALSE;
                }
            } else {
                $output['update'] = FALSE;
            }
            $output['old'] = $updater->source()->getVersionInstalled();
            $output['new'] = $updater->source()->getVersionAvailable();
            return response()->json($output);
        });
        Route::get('/download-update', function (\Codedge\Updater\UpdaterManager $updater) {
            if($updater->source()->isNewVersionAvailable()) {
                $updater->source()->getVersionInstalled();
                $updater->source()->getVersionAvailable();
                if($updater->source()->update()){
                    Setting::where('key', 'app_version')->update(['value' => $updater->source()->getVersionAvailable()]);
                    Artisan::call('config:cache');
                    $output = [
                        'new_version' => $updater->source()->getVersionAvailable(),
                        'icon' => 'success',
                        'message' => 'Update berhasil',
                    ];
                } else {
                    $output = [
                        'new_version' => $updater->source()->getVersionAvailable(),
                        'icon' => 'error',
                        'message' => 'Update gagal',
                    ];
                }
            } else {
                $output = [
                    'new_version' => NULL,
                    'icon' => 'error',
                    'message' => 'File updater tidak ditemukan',
                ];
            }
            return response()->json($output);
        });
        Route::get('/{query}','ProktorController@index')->name('proktor.index');
        Route::get('/reset-login/{user_id}','ProktorController@reset_login')->name('proktor.reset_login');
        Route::get('/force-selesai/{id}','ProktorController@force_selesai')->name('proktor.force_selesai');
        Route::get('/proses-download/{query}/{offset}','ProktorController@proses_download')->name('proktor.download');
        Route::post('/simpan/{query}','ProktorController@simpan')->name('proktor.simpan');
        Route::get('/hitung-data/{query}/{jumlah}','ProktorController@hitung_data')->name('proktor.hitung_data');
        Route::get('/referensi/detil/{query}/{id}','ReferensiController@detil')->name('referensi.detil');
        Route::get('/referensi/hapus/{query}/{id}','ReferensiController@hapus')->name('referensi.hapus');
    });
    Route::group(['middleware' => ['role:peserta_didik|ptk']], function () {
        Route::prefix('ujian')->group(function () {
            Route::get('/token','UjianController@token')->name('ujian.token');
            Route::get('/get-soal','UjianController@get_soal')->name('ujian.get_soal');
            Route::post('/konfirmasi','UjianController@konfirmasi')->name('ujian.konfirmasi');
            Route::get('/list','UjianController@all_ujian')->name('ujian.all_ujian');
            Route::get('/proses/{ujian_id}','UjianController@index')->name('ujian.proses');
            Route::get('/hasil','UjianController@hasil')->name('ujian.hasil');
            Route::get('/detil-hasil/{id}','UjianController@detil_hasil')->name('ujian.detil_hasil');
            Route::get('/selesai','UjianController@selesai')->name('ujian.selesai');
        });
    });
});