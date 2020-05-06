<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jurusan_sp extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'jurusan_sp';
	protected $primaryKey = 'jurusan_sp_id';
	protected $guarded = [];
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
    }
    public function jurusan(){
		return $this->hasOne('App\Jurusan', 'jurusan_id', 'jurusan_id');
    }
}
