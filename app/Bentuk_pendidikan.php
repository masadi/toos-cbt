<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bentuk_pendidikan extends Model
{
    public $incrementing = false;
    protected $table = 'bentuk_pendidikan';
	protected $primaryKey = 'bentuk_pendidikan_id';
	protected $guarded = [];
}
