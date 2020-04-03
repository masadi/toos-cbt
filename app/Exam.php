<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatsDate;
class Exam extends Model
{
    use FormatsDate;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'exams';
	protected $primaryKey = 'exam_id';
    protected $guarded = [];
    public function pembelajaran(){
		return $this->hasOne('App\Pembelajaran', 'pembelajaran_id', 'pembelajaran_id');
    }
    public function user_exam(){
		return $this->hasOne('App\User_exam', 'exam_id', 'exam_id');
    }
    public function question(){
		return $this->hasMany('App\Question', 'exam_id', 'exam_id');
    }
    public function list_soal(){
        return $this->hasManyThrough(
            'App\User_question',
            'App\User_exam',
            'exam_id', // Foreign key on users table...
            'user_exam_id', // Foreign key on posts table...
            'exam_id', // Local key on countries table...
            'user_exam_id' // Local key on users table...
        );
    }
    public function user_question(){
        return $this->hasManyThrough(
            'App\User_question',
            'App\User_exam',
            'exam_id', // Foreign key on users table...
            'user_exam_id', // Foreign key on posts table...
            'exam_id', // Local key on countries table...
            'user_exam_id' // Local key on users table...
        )->whereNotNull('answer_id');
    }
}
