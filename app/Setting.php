<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'settings';
	protected $primaryKey = 'key';
	protected $guarded = [];
}
