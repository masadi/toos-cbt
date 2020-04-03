<?php

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
Auth::routes();
Route::group(['middleware' => ['auth', 'get.menu']], function () {
    //Route::get('/', function () {           return view('dashboard.homepage'); });
    Route::get('/',      'HomeController@index');
    Route::get('/signout',      'HomeController@signout');
    Route::get('/reset-login/{password}','HomeController@reset_login')->name('proktor.reset_login');
    Route::group(['middleware' => ['role:proktor']], function () {
        Route::get('/{query}','ProktorController@index')->name('proktor.index');
        Route::get('/proses-download/{query}','ProktorController@proses_download')->name('proktor.download');
        Route::post('/simpan/{query}','ProktorController@simpan')->name('proktor.simpan');
        Route::get('/hitung-data/{query}/{jumlah}','ProktorController@hitung_data')->name('proktor.hitung_data');
        Route::prefix('referensi')->group(function () {
            Route::get('/tambah-data/{query}','ReferensiController@tambah_data')->name('referensi.tambah_data');
            Route::get('/detil/{query}/{id}','ReferensiController@detil')->name('referensi.detil');
            Route::get('/hapus/{query}/{id}','ReferensiController@hapus')->name('referensi.hapus');
            Route::get('/lisensi/{id}','ReferensiController@lisensi')->name('referensi.lisensi');
            Route::post('/simpan-data/{query}','ReferensiController@simpan')->name('referensi.simpan');
            Route::post('/saveBulk/{query}', 'ReferensiController@saveBulk')->name('referensi.saveBulk');
        });
        Route::prefix('materi')->group(function () {
            Route::get('/bank-soal','MateriController@index')->name('materi.bank_soal');
            Route::get('/ujian','MateriController@ujian')->name('materi.ujian');
            Route::get('/tambah-data/{query}','MateriController@tambah_data')->name('materi.tambah_data');
            Route::get('/detil/{query}/{id}','MateriController@detil')->name('materi.detil');
            Route::get('/hapus/{query}/{id}','MateriController@hapus')->name('materi.hapus');
            Route::post('/simpan-data/{query}','MateriController@simpan')->name('materi.simpan');
            Route::post('/saveBulk/{query}', 'MateriController@saveBulk')->name('materi.saveBulk');
        });
        Route::prefix('ajax')->group(function () {
            Route::get('/get-all-{query}','AjaxController@get_all_data')->name('ajax.get_all_data');
            Route::get('/get-all-materi/{query}','AjaxController@get_all_materi')->name('ajax.get_all_materi');
            Route::get('/get-all-soal/{ujian_id}','AjaxController@get_all_soal')->name('ajax.get_all_soal');
            Route::get('/get-soal/{pembelajaran_id}','AjaxController@get_soal')->name('ajax.get_soal');
            Route::post('/get-wilayah','AjaxController@get_wilayah')->name('ajax.get_wilayah');
            //Route::get('/get-all-{query}','AjaxController@get_all_data')->name('ajax.get_all_data');
            Route::post('/get-data-{query}','AjaxController@get_data')->name('ajax.get_data');
        });
        Route::prefix('ujian')->group(function () {
            Route::get('/soal/{ujian_id}','UjianController@soal')->name('ujian.soal');
            Route::get('/proses/{ujian_id}','UjianController@index')->name('ujian.proses');
            Route::get('/tambah-data/{query}/{ujian_id}','UjianController@tambah_data')->name('ujian.tambah_data');
            Route::post('/simpan-data/{query}/{ujian_id}','UjianController@simpan')->name('ujian.simpan');
            Route::get('/insert-soal/{ujian_id}/{id}','UjianController@insert_soal')->name('ujian.insert_soal');
        });
        Route::group(['middleware' => ['role:admin|sekolah|ptk|proktor|peserta_didik|user']], function () {
            Route::get('/users/profile','UsersController@profile')->name('users.profile');
            Route::put('/users/update-data/{id}','UsersController@update_data')->name('users.update_data');
            Route::get('/users/delete/{id}','UsersController@destroy')->name('users.destroy');
            Route::resource('users',        'UsersController')->except( ['create', 'store'] );
        });
    });
    Route::group(['middleware' => ['role:peserta_didik']], function () {
        Route::prefix('ujian')->group(function () {
            Route::get('/','UjianController@index')->name('ujian.list');
            Route::get('/hasil','UjianController@hasil')->name('ujian.hasil');
            Route::get('/detil-hasil/{id}','UjianController@detil_hasil')->name('ujian.detil_hasil');
            Route::get('/selesai','UjianController@selesai')->name('ujian.selesai');
        });
        Route::prefix('ajax')->group(function () {
            Route::get('/get-detil-hasil-ujian/{id}','AjaxController@get_detil_hasil_ujian')->name('ajax.get_detil_hasil_ujian');
        });
    });
});