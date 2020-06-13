<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $guarded = [];
    public function rombongan_belajar(){
		  return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
    public function pembelajaran(){
        return $this->hasOne('App\Pembelajaran', 'pembelajaran_id', 'pembelajaran_id');
  }
  public function exam(){
    return $this->hasOne('App\Exam', 'exam_id', 'exam_id');
  }
}
