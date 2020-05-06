<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatsDate;
use Str;
class User_question extends Model
{
    use FormatsDate;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'user_questions';
	protected $primaryKey = 'user_question_id';
    protected $guarded = [];
    public function soal(){
		return $this->hasOne('App\Question', 'question_id', 'question_id');
    }
    public function answer(){
		return $this->hasOne('App\Answer', 'answer_id', 'answer_id');
    }
    public function answers(){
		return $this->hasMany('App\Answer', 'question_id', 'question_id')->orderBy('jawaban_ke');
    }
    public function user_exam(){
		return $this->hasOne('App\User_exam', 'user_exam_id', 'user_exam_id');
  }
  public function peserta_didik(){
		return $this->hasOneThrough(
      'App\Peserta_didik',
      'App\Anggota_rombel',
      'anggota_rombel_id', // Foreign key on users table...
      'peserta_didik_id', // Foreign key on history table...
      'anggota_rombel_id', // Local key on suppliers table...
      'peserta_didik_id' // Local key on users table...
    );
  }
	protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
				//$model->uuid = Generator::uuid4()->toString();
				$model->{$model->getKeyName()} = Str::uuid()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
}
