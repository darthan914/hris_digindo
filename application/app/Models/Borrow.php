<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    protected $table = 'borrow';

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_employee');
    }
}
