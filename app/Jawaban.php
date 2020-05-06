<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Support\Database\CacheQueryBuilder;
use Str;
class Jawaban extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'jawaban';
	protected $primaryKey = 'jawaban_id';
	protected $guarded = [];
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
