<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dayoff extends Model
{
    protected $table = 'dayoff';

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_employee');
    }
}
