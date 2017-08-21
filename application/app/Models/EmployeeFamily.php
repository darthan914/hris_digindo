<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeFamily extends Model
{
    protected $table = 'employee_family';

    public function employee()
    {
    	return $this->belongsto('App\Employee', 'id_employee');
    }
}
