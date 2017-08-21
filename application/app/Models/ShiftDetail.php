<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShiftDetail extends Model
{
    protected $table = 'shift_detail';

    public function shift()
    {
    	return $this->belongsTo('App\Shift', 'id_shift');
    }
}
