<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AbsenceEmployee extends Model
{
    protected $table = 'absence_employee';

    public function absence()
    {
    	return $this->belongsTo('App\Absence', 'id_absence');
    }

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_machine', 'id_machine');
    }

    public function absenceEmployeeDetail()
    {
    	return $this->hasMany('App\AbsenceEmployeeDetail', 'id_absence_employee');
    }
}
