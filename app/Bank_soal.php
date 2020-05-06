<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Database\CacheQueryBuilder;
use Str;
class Bank_soal extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'bank_soal';
	protected $primaryKey = 'bank_soal_id';
	protected $guarded = [];
	public function mata_pelajaran(){
		return $this->hasOne('App\Mata_pelajaran', 'mata_pelajaran_id', 'mata_pelajaran_id');
    }
    public function ptk(){
		return $this->hasOne('App\Ptk', 'ptk_id', 'ptk_id');
    }
    public function question(){
		return $this->hasOne('App\Question', 'bank_soal_id', 'bank_soal_id');
    }
    public function jawaban(){
		return $this->hasMany('App\Jawaban', 'bank_soal_id', 'bank_soal_id')->orderBy('jawaban_ke');
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
