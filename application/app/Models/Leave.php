<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leave';

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_employee');
    }
}
