<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Str;
class Answer extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'answers';
	protected $primaryKey = 'answer_id';
    protected $guarded = [];
    public function question(){
		return $this->hasOne('App\Question', 'question_id', 'question_id');
    }
    public function user_answer(){
		return $this->hasOne('App\User_Question', 'answer_id', 'answer_id');
	}
}
