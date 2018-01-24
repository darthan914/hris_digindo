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
use Datatables;

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

	public function datatables(Request $request)
    {
        $index = Overtime::join('employee', 'overtime.id_employee', 'employee.id')->select('overtime.*', 'employee.name')->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('edit-overtime'))
            {
                $html .= '
                    <a href="' . route('admin.overtime.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('delete-overtime'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-overtime" data-toggle="modal" data-target="#delete-overtime" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            if(Auth::user()->can('confirm-overtime') && $index->check_leader == 0)
            {
                $html .= '
                    <button class="btn btn-xs btn-success confirm-overtime" data-toggle="modal" data-target="#confirm-overtime" data-id="'.$index->id.'"><i class="fa fa-check"></i></button>
                ';
            }

            if(Auth::user()->can('confirm-overtime') && $index->check_leader == 1)
            {
                $html .= '
                    <button class="btn btn-xs btn-warning cancel-overtime" data-toggle="modal" data-target="#cancel-overtime" data-id="'.$index->id.'"><i class="fa fa-times"></i></button>
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

        $datatables->editColumn('end_overtime', function ($index) {
            return date('d/m/Y H:i:s', strtotime($index->end_overtime));
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

		return view('backend.overtime.create')->with(compact('employee'));
	}

	public function store(Request $request)
	{

		$message = [
			'id_employee.required'  => 'mohon pilih karyawan',
			'id_employee.integer'   => 'karyawan tidak valid',
			'date.required'         => 'mohon diisi',
			'date.date'             => 'format dalam bentuk tanggal',
			'end_overtime.required' => 'mohon diisi',
			'end_overtime.date'     => 'format dalam bentuk tanggal dan jam',
			'note.required'         => 'mohon diisi',
		];

		$validator = Validator::make($request->all(), [
			'id_employee'  => 'required|integer',
			'date'         => 'required|date',
			'end_overtime' => 'required|date',
			'note'         => 'required',
		], $message);

		if($validator->fails())
		{
			return redirect()->route('admin.overtime.create')->withErrors($validator)->withInput();
		}

		$index = new Overtime;

		$index->id_employee  = $request->id_employee;
		$index->date         = date('Y-m-d', strtotime($request->date));
		$index->end_overtime = date('Y-m-d H:i:s', strtotime($request->end_overtime));
		$index->note         = $request->note;
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

	public function delete(Request $request)
	{
		Overtime::destroy($request->id);

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

	public function confirm(Request $request)
    {
        $index = Overtime::find($request->id);

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
        return redirect()->route('admin.overtime');
    }
}
