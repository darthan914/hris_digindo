<?php

namespace App\Http\Controllers\Backend;

use App\Overtime;
use App\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use File;
use Hash;
use Validator;

use App\Http\Controllers\Controller;

class OvertimeController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		$index = Overtime::all();

		return view('backend.overtime.index')->with(compact('index'));
	}

	public function create()
	{
		$employee = Employee::all();

		return view('backend.overtime.create')->with(compact('employee'));
	}

	public function store(Request $request)
	{

		$message = [
			'id_employee.required' => 'mohon pilih karyawan',
			'id_employee.integer' => 'karyawan tidak valid',
			'date.required' => 'mohon diisi',
			'date.date' => 'format dalam bentuk tanggal',
			'end_overtime.required' => 'mohon diisi',
			'end_overtime.date' => 'format dalam bentuk tanggal dan jam',
			'note.required' => 'mohon diisi',
		];

		$validator = Validator::make($request->all(), [
			'id_employee' => 'required|integer',
			'date' => 'required|date',
			'end_overtime' => 'required|date',
			'note' => 'required',
		], $message);

		if($validator->fails())
		{
			return redirect()->route('admin.overtime.create')->withErrors($validator)->withInput();
		}

		$index = new Overtime;

		$index->id_employee = $request->id_employee;
		$index->date = date('Y-m-d', strtotime($request->date));
		$index->end_overtime = date('Y-m-d H:i:s', strtotime($request->end_overtime));
		$index->note = $request->note;
		$index->check_leader = 0;
		
		$index->save();

		Session::flash('success', 'Data Has Been Added');
		return redirect()->route('admin.overtime');
	}

	public function edit($id)
	{
		$index = Overtime::find($id);
		$employee = Employee::all();

		return view('backend.overtime.edit')->with(compact('index', 'employee'));
	}

	public function update($id, Request $request)
	{
		$message = [
			'id_employee.required' => 'mohon pilih karyawan',
			'id_employee.integer' => 'karyawan tidak valid',
			'date.required' => 'mohon diisi',
			'date.date' => 'format dalam bentuk tanggal',
			'end_overtime.required' => 'mohon diisi',
			'end_overtime.date' => 'format dalam bentuk tanggal dan jam',
			'note.required' => 'mohon diisi',
		];

		$validator = Validator::make($request->all(), [
			'id_employee' => 'required|integer',
			'date' => 'required|date',
			'end_overtime' => 'required|date',
			'note' => 'required',
		], $message);

		if($validator->fails())
		{
			return redirect()->route('admin.overtime.edit', ["id" => $id])->withErrors($validator)->withInput();
		}

		$index = Overtime::find($id);

		$index->id_employee = $request->id_employee;
		$index->date = date('Y-m-d', strtotime($request->date));
		$index->end_overtime = date('Y-m-d H:i:s', strtotime($request->end_overtime));
		$index->note = $request->note;
		$index->check_leader = 0;
		
		$index->save();

		Session::flash('success', 'Data Has Been Updated');
		return redirect()->route('admin.overtime');
	}

	public function delete($id)
	{
		Overtime::destroy($id);

		Session::flash('success', 'Data Has Been Deleted');
		return redirect()->route('admin.overtime');
	}

	public function action(Request $request)
	{
		if($request->action == 'delete')
		{
			Overtime::destroy($request->id);
			Session::flash('success', 'Data Selected Has Been Deleted');
		}
		else if($request->action == 'enable')
		{
			$index = Overtime::whereIn('id', $request->id)->update(['active' => 1]);
			Session::flash('success', 'Data Selected Has Been Actived');
		}
		else if($request->action == 'disable')
		{
			$index = Overtime::whereIn('id', $request->id)->update(['active' => 0]);
			Session::flash('success', 'Data Selected Has Been Inactived');
		}
		
		return redirect()->route('admin.overtime');
	}

	public function active($id, $action)
	{
		$index = Overtime::find($id);

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

		return redirect()->route('admin.overtime');
	}
}
