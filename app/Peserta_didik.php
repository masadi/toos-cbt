<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Peserta_didik extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'peserta_didik';
	protected $primaryKey = 'peserta_didik_id';
	protected $guarded = [];
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
	}
	public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'peserta_didik_id', 'peserta_didik_id')->where('semester_id', config('global.semester_id'));
	}
	public function agama(){
		return $this->hasOne('App\Agama', 'agama_id', 'agama_id');
	}
	public function user(){
		return $this->hasOne('App\User', 'peserta_didik_id', 'peserta_didik_id');
	}
}
