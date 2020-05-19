<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/reset/ujian','HomeController@reset_ujian')->name('reset_ujian');
Route::get('/browser-tidak-support/{name}', function($name){
    return view('update_browser', compact('name'));
})->name('update_browser');
Auth::routes();
Route::get('/logout', 'FrontController@logout');
Route::group(['middleware' => ['auth', 'get.route']], function () {
    Route::get('/','HomeController@index')->name('index');
    Route::get('/home','HomeController@index')->name('home');
    Route::group(['middleware' => ['role:proktor']], function () {
        Route::get('/kirim-wa','ProktorController@kirim_wa')->name('proktor.kirim_wa');
        Route::get('/{query}','ProktorController@index')->name('proktor.index');
        Route::get('/reset-login/{user_id}','ProktorController@reset_login')->name('proktor.reset_login');
        Route::get('/cetak-kartu/{id}','ProktorController@cetak_kartu')->name('proktor.cetak_kartu');
        Route::get('/force-selesai/{id}','ProktorController@force_selesai')->name('proktor.force_selesai');
        Route::get('/proses-download/{query}/{offset}','ProktorController@proses_download')->name('proktor.download');
        Route::post('/simpan/{query}','ProktorController@simpan')->name('proktor.simpan');
        Route::get('/hitung-data/{query}/{jumlah}','ProktorController@hitung_data')->name('proktor.hitung_data');
        Route::get('/referensi/detil/{query}/{id}','ReferensiController@detil')->name('referensi.detil');
        Route::get('/referensi/hapus/{query}/{id}','ReferensiController@hapus')->name('referensi.hapus');
    });
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
            Route::post('/update-pengguna','AjaxController@update_pengguna')->name('ujian.update_pengguna');
        });
    });
});
