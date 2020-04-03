<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    public $incrementing = false;
	protected $table = 'agama';
	protected $primaryKey = 'agama_id';
	protected $guarded = [];
}
