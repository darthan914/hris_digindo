<?php

namespace App\Http\Controllers\Backend;

use App\Dayoff;
use App\Holiday;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CalenderController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
	{
    	// index Dayoff
    	$dayoff = Dayoff::join('employee', 'employee.id', '=', 'dayoff.id_employee')
    	->select(DB::raw('dayoff.*, name'))
    	->orderBy('id', 'DESC')->get();

    	// index holiday
    	$holiday = Holiday::orderBy('date', 'ASC')->get();

    	return view('backend.calender.index')->with(compact('dayoff', 'holiday'));
    }

}
