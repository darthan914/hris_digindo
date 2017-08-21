<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $table = 'absence';

    public function absenceEmployee()
    {
        return $this->hasMany('App\AbsenceEmployee', 'id_employee');
    }
}
