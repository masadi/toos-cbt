<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    public $incrementing = false;
	protected $table = 'kurikulum';
	protected $primaryKey = 'kurikulum_id';
	protected $guarded = [];
}
