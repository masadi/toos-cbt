<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatsDate;
class Setting extends Model
{
    use FormatsDate;
    //public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
	protected $table = 'settings';
	protected $primaryKey = 'key';
	protected $guarded = [];
}
