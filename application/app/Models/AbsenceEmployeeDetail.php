<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbsenceEmployeeDetail extends Model
{
    protected $table = 'absence_employee_detail';

    public function absenceEmployee()
    {
    	return $this->belongsto('App\AbsenceEmployee', 'id_absence_employee');
    }
}
