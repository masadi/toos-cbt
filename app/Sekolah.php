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
	public function jurusan_sp(){
		return $this->hasMany('App\Jurusan_sp', 'jurusan_sp_id', 'jurusan_sp_id');
	}
}
