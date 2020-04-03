<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jenjang_pendidikan extends Model
{
    public $incrementing = false;
    protected $table = 'jenjang_pendidikan';
	protected $primaryKey = 'jenjang_pendidikan_id';
	protected $guarded = [];
}
