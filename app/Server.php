<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class Server extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'servers';
	protected $primaryKey = 'server_id';
	protected $guarded = [];
	public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
    }
    public function rombongan_belajar(){
		return $this->hasOne('App\Rombongan_belajar', 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
}
