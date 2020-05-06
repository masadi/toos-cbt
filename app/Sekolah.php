<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'sekolah';
	protected $primaryKey = 'sekolah_id';
	protected $guarded = [];
	public function bentuk_pendidikan(){
		return $this->hasOne('App\Bentuk_pendidikan', 'bentuk_pendidikan_id', 'bentuk_pendidikan_id');
	}
	public function peserta_event(){
		return $this->hasOne('App\Peserta_event', 'sekolah_id', 'sekolah_id');
	}
	public function jurusan_sp(){
		return $this->hasMany('App\Jurusan_sp', 'sekolah_id', 'sekolah_id');
	}
	public function ptk(){
		return $this->hasMany('App\Ptk', 'sekolah_id', 'sekolah_id');
	}
	public function peserta_didik(){
		return $this->hasMany('App\Peserta_didik', 'sekolah_id', 'sekolah_id');
	}
	public function rombongan_belajar(){
		return $this->hasMany('App\Rombongan_belajar', 'sekolah_id', 'sekolah_id');
	}
}
