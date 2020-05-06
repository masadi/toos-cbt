<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anggota_rombel extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'anggota_rombel';
	protected $primaryKey = 'anggota_rombel_id';
	protected $guarded = [];
	public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function peserta_didik(){
		return $this->hasOne('App\Peserta_didik', 'peserta_didik_id', 'peserta_didik_id');
	}
	public function user_exam(){
		return $this->hasOne('App\User_exam', 'anggota_rombel_id', 'anggota_rombel_id')->where('status_ujian', 1);
	}
	public function jejak_ujian(){
		return $this->hasMany('App\User_exam', 'anggota_rombel_id', 'anggota_rombel_id')->where('status_ujian', 0);
	}
}
