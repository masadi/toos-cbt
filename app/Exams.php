<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Str;
class Exams extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'exams';
	protected $primaryKey = 'exam_id';
    protected $guarded = [];
    public function pembelajaran(){
		return $this->hasOne('App\Pembelajaran', 'pembelajaran_id', 'pembelajaran_id');
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
