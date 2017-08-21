<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookContract extends Model
{
    protected $table = 'book_contract';

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_employee');
    }
}
