<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    protected $table = 'job_title';

    public function employee()
    {
    	return $this->hasMany('App\Employee', 'id_job_title');
    }

    public function attendance()
    {
    	return $this->hasMany('App\Attendance', 'id_job_title');
    }
}
