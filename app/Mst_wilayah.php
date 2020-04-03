<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mst_wilayah extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'mst_wilayah';
	protected $primaryKey = 'kode_wilayah';
	protected $guarded = [];
	public function parent(){
		return $this->belongsTo(Mst_wilayah::class, 'mst_kode_wilayah')->with('parent');
	}
}
