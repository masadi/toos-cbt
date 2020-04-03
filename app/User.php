<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasLocalDates;
use Str;
class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasRoles, HasLocalDates;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password', 'sekolah_id', 'ptk_id', 'peserta_didik_id', 'timezone', 'menuroles',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at'
    ];

    //protected $attributes = [ 
        //'menuroles' => 'user',
    //];
    public function sekolah(){
		return $this->hasOne('App\Sekolah', 'sekolah_id', 'sekolah_id');
    }
    public function peserta_didik(){
		return $this->hasOne('App\Peserta_didik', 'peserta_didik_id', 'peserta_didik_id');
    }
    public function ptk(){
		return $this->hasOne('App\Ptk', 'ptk_id', 'ptk_id');
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
