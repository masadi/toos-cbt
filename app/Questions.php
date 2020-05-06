<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'questions';
	protected $primaryKey = 'question_id';
	protected $guarded = [];
}
