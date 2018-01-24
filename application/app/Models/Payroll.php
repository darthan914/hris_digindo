<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payroll';

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_employee');
    }
}
