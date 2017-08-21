<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';

    public function jobTitle()
    {
    	return $this->belongsto('App\JobTitle', 'id_job_title');
    }

    public function employeeFamily()
    {
    	return $this->hasMany('App\EmployeeFamily', 'id_employee');
    }

    public function bookContract()
    {
        return $this->hasMany('App\BookContract', 'id_employee');
    }

    public function bookPayrollChange()
    {
        return $this->hasMany('App\BookPayrollChange', 'id_employee');
    }

    public function dayoff()
    {
        return $this->hasMany('App\Dayoff', 'id_employee');
    }

    public function itemBorrowed()
    {
        return $this->hasMany('App\ItemBorrowed', 'id_employee');
    }

    public function leave()
    {
        return $this->hasMany('App\Leave', 'id_employee');
    }

    public function absenceEmployee()
    {
        return $this->hasMany('App\AbsenceEmployee', 'id_machine');
    }

    public function overtime()
    {
    	return $this->hasMany('App\Overtime', 'id_employee');
    }
}
