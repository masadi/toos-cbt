<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    protected $guarded = [];
    public function mata_pelajaran(){
		  return $this->hasOne('App\Mata_pelajaran', 'mata_pelajaran_id', 'mata_pelajaran_id');
    }
    public function event(){
		  return $this->hasOne('App\Event', 'id', 'event_id');
    }
}
