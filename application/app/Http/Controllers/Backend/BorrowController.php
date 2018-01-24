<?php

namespace App\Http\Controllers\Backend;

use App\Borrow;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Session;
use File;
use Datatables;

use App\Http\Controllers\Controller;

class BorrowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        return view('backend.borrow.index')->with(compact('request'));
    }

    public function datatables(Request $request)
    {
        $index = Borrow::join('employee', 'borrow.id_employee', 'employee.id')->select('borrow.*', 'employee.name')->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('edit-borrow'))
            {
                $html .= '
                    <a href="' . route('admin.borrow.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('delete-borrow'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-borrow" data-toggle="modal" data-target="#delete-borrow" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            return $html;
        });

        $datatables->editColumn('status', function ($index) {
            $html = '';
            if($index->status)
            {
                $html .= '
                    <span class="label label-info">Dipinjam</span>
                ';
            }
            else
            {
                $html .= '
                    <span class="label label-info">Dikembalikan</span>
                ';
            }
            return $html;
        });

        $datatables->editColumn('date_borrow', function ($index) {
            return date('d/m/Y', strtotime($index->date_borrow));
        });

        $datatables->editColumn('date_return', function ($index) {
            return date('d/m/Y', strtotime($index->date_return));
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

        return view('backend.borrow.create')->with(compact('employee'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'id_employee' => 'required',
            'item'        => 'required',
            'date_borrow' => 'nullable|date',
            'date_return' => 'nullable|date',
        ]);

        $index = new Borrow;

        $index->id_employee = $request->id_employee;
        $index->item        = $request->item;
        $index->date_borrow = date('Y-m-d', strtotime($request->date_borrow));
        $index->date_return = date('Y-m-d', strtotime($request->date_return));
        $index->note        = $request->note;
        $index->status      = isset($request->status) ? 1 : 0;

        $index->save();

        Session::flash('success', 'Data Has Been Added');
        return redirect()->route('admin.borrow');
    }

    public function edit($id)
    {
        $index = Borrow::find($id);

        $employee = Employee::all();

        return view('backend.borrow.edit')->with(compact('index', 'employee'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'id_employee' => 'required',
            'item'        => 'required',
            'date_borrow' => 'nullable|date',
            'date_return' => 'nullable|date',
        ]);

        $index = Borrow::find($id);

        $index->id_employee = $request->id_employee;
        $index->item        = $request->item;
        $index->date_borrow = date('Y-m-d', strtotime($request->date_borrow));
        $index->date_return = date('Y-m-d', strtotime($request->date_return));
        $index->note        = $request->note;
        $index->status      = isset($request->status) ? 1 : 0;
        
        $index->save();

        Session::flash('success', 'Data Has Been Updated');
        return redirect()->route('admin.borrow');
    }

    public function delete(Request $request)
    {
        Borrow::destroy($request->id);

        Session::flash('success', 'Data Has Been Deleted');
        return redirect()->route('admin.borrow');
    }

    public function action(Request $request)
    {
        if($request->action == 'delete')
        {
            Borrow::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
        }
        else if($request->action == 'enable')
        {
            $index = Borrow::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
        }
        else if($request->action == 'disable')
        {
            $index = Borrow::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
        }
        
        return redirect()->route('admin.borrow');
    }

    public function active($id, $action)
    {
        $index = Borrow::find($id);

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

        return redirect()->route('admin.borrow');
    }
}
