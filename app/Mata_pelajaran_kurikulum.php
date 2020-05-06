<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mata_pelajaran_kurikulum extends Model
{
    public $incrementing = false;
	protected $table = 'mata_pelajaran_kurikulum';
	protected $primaryKey = ['kurikulum_id', 'mata_pelajaran_id', 'tingkat_pendidikan_id'];
	protected $guarded = [];
	public function mata_pelajaran(){
		return $this->hasOne('App\Mata_pelajaran', 'mata_pelajaran_id', 'mata_pelajaran_id');
    }
	public function kurikulum(){
		return $this->hasOne('App\Kurikulum', 'kurikulum_id', 'kurikulum_id');
	}
	public function tingkat_pendidikan(){
		return $this->hasOne('App\Tingkat_pendidikan', 'tingkat_pendidikan_id', 'tingkat_pendidikan_id');
	}
	public function pembelajaran(){
		return $this->hasOne('App\Pembelajaran', 'mata_pelajaran_id', 'mata_pelajaran_id');
    }
}
