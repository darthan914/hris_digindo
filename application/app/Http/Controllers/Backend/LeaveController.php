<?php

namespace App\Http\Controllers\Backend;

use App\Leave;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use File;

use App\Http\Controllers\Controller;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
	{
		$index = Leave::orderBy('id', 'DESC')
        	->get();

    	return view('backend.leave.index')->with(compact('index'));
	}

	public function create()
    {
    	$employee = Employee::all();

    	return view('backend.leave.create')->with(compact('employee'));
    }

	public function store(Request $request)
    {

    	$this->validate($request, [
			'id_employee' => 'required|integer',
			'date'        => 'required|date',
			'start_time'  => 'required|before:end_time',
			'end_time'    => 'required|after:start_time',
			'need'        => 'required',
        ]);

    	$index = new Leave;

		$index->id_employee = $request->id_employee;
		$index->date        = date('Y-m-d', strtotime($request->date));
		$index->start_time  = date('H:i:s', strtotime($request->start_time));
		$index->end_time    = date('H:i:s', strtotime($request->end_time));
		$index->need        = $request->need;
		$index->note        = $request->note;

        $index->save();

        Session::flash('success', 'Data Has Been Added');
    	return redirect()->route('admin.leave');
    }

    public function edit($id)
    {
    	$index = Leave::find($id);

    	$employee = Employee::all();

    	return view('backend.leave.edit')->with(compact('index', 'employee'));
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
            'id_employee' => 'required|integer',
			'date'        => 'required|date',
			'start_time'  => 'required|before:end_time',
			'end_time'    => 'required|after:start_time',
			'need'        => 'required',
        ]);

    	$index = Leave::find($id);

        $index->id_employee = $request->id_employee;
		$index->date        = date('Y-m-d', strtotime($request->date));
		$index->start_time  = date('H:i:s', strtotime($request->start_time));
		$index->end_time    = date('H:i:s', strtotime($request->end_time));
		$index->need        = $request->need;
		$index->note        = $request->note;
        
        $index->save();

        Session::flash('success', 'Data Has Been Updated');
    	return redirect()->route('admin.leave');
    }

    public function delete($id)
    {
    	Leave::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
    	return redirect()->route('admin.leave');
    }

    public function action(Request $request)
    {
    	if($request->action == 'delete')
    	{
    		Leave::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
    	}
    	else if($request->action == 'enable')
    	{
    		$index = Leave::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
    	}
    	else if($request->action == 'disable')
    	{
    		$index = Leave::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
    	}
    	
    	return redirect()->route('admin.leave');
    }

    public function active($id, $action)
    {
        $index = Leave::find($id);

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

        return redirect()->route('admin.leave');
    }
}
