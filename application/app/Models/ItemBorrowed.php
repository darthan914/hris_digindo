<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemBorrowed extends Model
{
    protected $table = 'item_borrowed';

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_employee');
    }
}
