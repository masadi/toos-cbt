<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tingkat_pendidikan extends Model
{
    public $incrementing = false;
    protected $table = 'tingkat_pendidikan';
	protected $primaryKey = 'tingkat_pendidikan_id';
	protected $guarded = [];
}
