<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
class Question extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'questions';
	protected $primaryKey = 'question_id';
    protected $guarded = [];
    public function exam(){
		return $this->hasOne('App\Exam', 'exam_id', 'exam_id');
    }
    public function correct(){
		return $this->hasOne('App\Answer', 'question_id', 'question_id')->where('correct', 1);
    }
    public function answers(){
		return $this->hasMany('App\Answer', 'question_id', 'question_id')->orderBy('jawaban_ke');
    }
    public function user_question(){
		return $this->hasOne('App\User_question', 'question_id', 'question_id');
    }
}