<?php

namespace App\Http\Controllers\Backend;

use App\Attendance;
use App\JobTitle;
use App\Shift;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use File;

use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
	{
		$f_id_shift = 0;
		if(isset($request->f_id_shift))
		{
			$f_id_shift = $request->f_id_shift;
		}
		$shift = shift::all();

		$index = JobTitle::leftJoin('attendance', 'attendance.id_job_title', '=', 'job_title.id')
		->leftJoin('shift', 'shift.id', '=', 'attendance.id_shift')
		->select(DB::raw('attendance.id, job_title.id as job_title_id, job_title.name as job_title_name, shift.id as shift_id, shift.name as shift_name'));

		if($f_id_shift)
		{
			$index->where('attendance.id_shift', $f_id_shift);
		}

		$index = $index->get();

    	return view('backend.attendance.index')->with(compact('index', 'shift', 'f_id_shift'));
	}

	public function create(Request $request)
    {
    	$jobTitle = JobTitle::all();
    	$shift    = Shift::all();
    	return view('backend.attendance.create')->with(compact('jobTitle', 'shift', 'request'));
    }

	public function store(Request $request)
    {

    	$this->validate($request, [
    		'id_job_title' => 'required|integer|unique:attendance,id_job_title',
			'id_shift' => 'required|integer',
        ]);

    	$index = new Attendance;

    	$index->id_job_title = $request->id_job_title;
		$index->id_shift = $request->id_shift;

        $index->save();

        Session::flash('success', 'Data Has Been Added');
    	return redirect()->route('admin.attendance');
    }

    public function edit($id)
    {
    	$index    = Attendance::find($id);
    	$jobTitle = JobTitle::all();
    	$shift    = Shift::all();

    	return view('backend.attendance.edit')->with(compact('index', 'jobTitle', 'shift'));
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
    		'id_job_title' => 'required|integer|unique:attendance,id_job_title,'.$id,
			'id_shift' => 'required|integer',
        ]);

    	$index = Attendance::find($id);

    	$index->id_job_title = $request->id_job_title;
		$index->id_shift = $request->id_shift;

        $index->save();

        Session::flash('success', 'Data Has Been Updated');
    	return redirect()->route('admin.attendance');
    }

    public function delete($id)
    {
    	Attendance::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
    	return redirect()->route('admin.attendance');
    }

    public function action(Request $request)
    {
    	if(isset($request->id) || isset($request->id_job_title))
    	{
    		if($request->action == 'delete')
	    	{
	    		Attendance::destroy($request->id);
	            Session::flash('success', 'Data Selected Has Been Deleted');
	    	}
	    	else if($request->action == 'enable')
	    	{
	    		$index = Attendance::whereIn('id', $request->id)->update(['active' => 1]);
	            Session::flash('success', 'Data Selected Has Been Actived');
	    	}
	    	else if($request->action == 'disable')
	    	{
	    		$index = Attendance::whereIn('id', $request->id)->update(['active' => 0]);
	            Session::flash('success', 'Data Selected Has Been Inactived');
	    	}
	    	else
	    	{
	    		if(isset($request->id))
	    		{
	    			$index = Attendance::whereIn('id', $request->id)->update(['id_shift' => $request->action]);
	            	Session::flash('success', 'Data Selected Has Been Changed');
	    		}
	    		
    			$count = 0;
		        $detail = [];
		        if (isset($request->id_job_title)) {
		            foreach ($request->id_job_title as $key) {
		                $array = [
		                	'id_job_title' => $request->id_job_title[$count],
							'id_shift' => $request->action,
		                ];
		                $count++;
		                array_push($detail, $array);
		            }
		        }

		        $attendance = Attendance::insert($detail);
	    	}
    	}
    	
    	return redirect()->route('admin.attendance');
    }

    public function active($id, $action)
    {
        $index = Attendance::find($id);

        $index->active = $action;

        $index->save();

        if($action == 1)
        {
            Session::flash('success', 'Data Has Been Actived');
        }
        else
        {
            Session::flash('success', 'Data Has Been Inactived');
        }

        return redirect()->route('admin.attendance');
    }
}
