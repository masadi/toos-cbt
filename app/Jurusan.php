<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    public $incrementing = false;
	protected $table = 'jurusan';
	protected $primaryKey = 'jurusan_id';
	protected $guarded = [];
}
