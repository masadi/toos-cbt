<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Peserta_event extends Model
{
    protected $guarded = [];
    public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
	}
}
