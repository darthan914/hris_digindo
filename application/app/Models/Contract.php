<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'contract';

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_employee');
    }
}
