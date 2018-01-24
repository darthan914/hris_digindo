<?php

namespace App\Http\Controllers\Backend;

use App\Leave;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Datatables;
use Session;
use File;

use App\Http\Controllers\Controller;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
	{
    	return view('backend.leave.index')->with(compact('request'));
	}

    public function datatables(Request $request)
    {
        $index = Leave::join('employee', 'leave.id_employee', 'employee.id')->select('leave.*', 'employee.name')->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('edit-leave'))
            {
                $html .= '
                    <a href="' . route('admin.leave.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('delete-leave'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-leave" data-toggle="modal" data-target="#delete-leave" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            if(Auth::user()->can('confirm-leave') && $index->check_leader == 0)
            {
                $html .= '
                    <button class="btn btn-xs btn-success confirm-leave" data-toggle="modal" data-target="#confirm-leave" data-id="'.$index->id.'"><i class="fa fa-check"></i></button>
                ';
            }

            if(Auth::user()->can('confirm-leave') && $index->check_leader == 1)
            {
                $html .= '
                    <button class="btn btn-xs btn-warning cancel-leave" data-toggle="modal" data-target="#cancel-leave" data-id="'.$index->id.'"><i class="fa fa-times"></i></button>
                ';
            }

            return $html;
        });

        $datatables->editColumn('check_leader', function ($index) {
            $html = '';
            if($index->check_leader)
            {
                $html .= '
                    <span class="label label-info">Konfirmasi</span>
                ';
            }
            else
            {
                $html .= '
                    <span class="label label-default">Belum Konfirmasi</span>
                ';
            }
            return $html;
        });

        $datatables->editColumn('date', function ($index) {
            return date('d/m/Y', strtotime($index->date));
        });

        $datatables->addColumn('check', function ($index) {
            $html = '';

            $html .= '
                <input type="checkbox" class="check" value="' . $index->id . '" name="id[]" form="action">
            ';

            return $html;
        });

        $datatables = $datatables->make(true);
        return $datatables;
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

    public function confirm(Request $request)
    {
        $index = Leave::find($request->id);

        if($index->check_leader)
        {
            $index->check_leader = 0;
        }
        else
        {
            $index->check_leader = 1;
        }

        $index->save();

        Session::flash('success', 'Data berhasil diupdate');
        return redirect()->route('admin.leave');
    }
}
