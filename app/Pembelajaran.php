<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Str;
class Pembelajaran extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'pembelajaran';
	protected $primaryKey = 'pembelajaran_id';
	protected $guarded = [];
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
	}
	public function ptk(){
		return $this->hasOne('App\Ptk', 'ptk_id', 'ptk_id');
	}
	public function mata_pelajaran(){
		return $this->hasOne('App\Mata_pelajaran', 'mata_pelajaran_id', 'mata_pelajaran_id');
    }
    public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function exam(){
		return $this->hasMany('App\Exam', 'pembelajaran_id', 'pembelajaran_id');
	}
}
