<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];
    public function getAvailableFromAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['available_from'])
        ->format('d/m/y');
    }
    public function getAvailableToAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['available_to'])
        ->format('d/m/y');
    }
    public function peserta(){
		return $this->hasMany('App\Peserta_event', 'event_id', 'id');
    }
}
