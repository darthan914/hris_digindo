<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    public function jobTitle()
    {
    	return $this->belongsTo('App\JobTitle', 'id_job_title');
    }

    public function shift()
    {
    	return $this->belongsTo('App\Shift', 'id_shift');
    }
}
