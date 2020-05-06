<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ptk extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'ptk';
	protected $primaryKey = 'ptk_id';
	protected $guarded = [];
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
	}
	public function agama(){
		return $this->hasOne('App\Agama', 'agama_id', 'agama_id');
	}
	public function pembelajaran(){
		return $this->hasMany('App\Pembelajaran', 'ptk_id', 'ptk_id')->where('semester_id', config('global.semester_id'));
	}
	public function user(){
		return $this->hasOne('App\User', 'ptk_id', 'ptk_id');
	}
	public function user_exam(){
		return $this->hasOne('App\User_exam', 'ptk_id', 'ptk_id')->where('status_ujian', 1);
	}
}
