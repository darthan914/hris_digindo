<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $table = 'overtime';

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }
}
