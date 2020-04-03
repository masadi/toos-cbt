<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatsDate;
use Str;
class User_exam extends Model
{
    use FormatsDate;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'user_exams';
	protected $primaryKey = 'user_exam_id';
    protected $guarded = [];
    public function exam(){
		return $this->hasOne('App\Exam', 'exam_id', 'exam_id');
    }
    public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'anggota_rombel_id', 'anggota_rombel_id');
    }
    public function ptk(){
		return $this->hasOne('App\Ptk', 'ptk_id', 'ptk_id');
    }
    public function user_question(){
		return $this->hasMany('App\User_question', 'user_exam_id', 'user_exam_id');
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
