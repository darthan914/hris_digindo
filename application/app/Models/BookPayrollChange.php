<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookPayrollChange extends Model
{
    protected $table = 'book_payroll_change';

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_employee');
    }
}
