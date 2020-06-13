<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Database\Eloquent\SoftDeletes;
//use Spatie\Permission\Traits\HasRoles;
//use App\Traits\HasLocalDates;
use Str;
use Laratrust\Traits\LaratrustUserTrait;
use Helper;
class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    //protected $fillable = [
        //'name', 'username', 'email', 'password', 'sekolah_id', 'ptk_id', 'peserta_didik_id', 'timezone', 'menuroles', 'default_password',
    //];

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
    public function anggota_rombel(){
		return $this->hasOne('App\Anggota_rombel', 'peserta_didik_id', 'peserta_didik_id');
    }
    public function peserta_didik(){
		return $this->hasOne('App\Peserta_didik', 'peserta_didik_id', 'peserta_didik_id');
    }
    public function ptk(){
		return $this->hasOne('App\Ptk', 'ptk_id', 'ptk_id');
    }
    public function adminlte_image()
    {
        return ($this->photo) ? asset('storage/images/'.$this->photo) : '/vendor/img/avatar3.png';
    }
    public function adminlte_desc()
    {
        return implode(',', $this->roles->map(function ($user) {
            return $user->display_name;
        })->toArray());
    }
    public function isLogout()
    {
        return $this->logout;
    }
    public function checkPassword(){
        if(Hash::check($this->default_password, $this->password)){
            return $this->default_password;
        }
        return '-';
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
    public function routeNotificationForWhatsApp()
    {
        return Helper::hp($this->phone_number);
    }
}
