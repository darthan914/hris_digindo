<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';

    public function attendance()
    {
    	return $this->hasMany('App\Attendance', 'id_shift');
    }

    public function shiftDetail()
    {
    	return $this->hasMany('App\ShiftDetail', 'id_shift');
    }
}
