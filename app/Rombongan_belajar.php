<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rombongan_belajar extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'rombongan_belajar';
	protected $primaryKey = 'rombongan_belajar_id';
	protected $guarded = [];
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
	}
	public function jurusan_sp(){
		return $this->hasOne('App\Jurusan_sp', 'jurusan_sp_id', 'jurusan_sp_id');
	}
	public function anggota_rombel(){
		return $this->hasMany('App\Anggota_rombel', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function jadwal(){
		return $this->hasMany('App\Jadwal', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function pembelajaran(){
		return $this->hasMany('App\Pembelajaran', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
}
