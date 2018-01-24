<?php

namespace App\Http\Controllers\Backend;

use App\Employee;
use App\Holiday;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Session;
use Datatables;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
	{
    	$year = Holiday::groupBy(DB::raw('YEAR(date)'))->select(DB::raw('YEAR(date) as year'))->get();

    	return view('backend.holiday.index')->with(compact('request', 'year'));
	}

    public function datatables(Request $request)
    {
        $f_year = $this->filter($request->f_year);

        $index = Holiday::orderBy('date', 'ASC');
        if($f_year != '')
        {
            $index = $index->whereYear('date', $f_year);
        }
        $index = $index->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('edit-holiday'))
            {
                $html .= '
                    <a href="' . route('admin.holiday.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('delete-holiday'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-holiday" data-toggle="modal" data-target="#delete-holiday" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
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
    	return view('backend.holiday.create');
    }

	public function store(Request $request)
    {

    	$this->validate($request, [
            'name' => 'required',
            'date' => 'required',
            'type' => 'required',
        ]);

    	$index = new Holiday;

        $index->name = $request->name;
        $index->date = date('Y-m-d', strtotime($request->date));
        $index->type = $request->type;
        
        $index->save();

        Session::flash('success', 'Data Has Been Added');
    	return redirect()->route('admin.holiday');
    }

    public function edit($id)
    {
    	$index = Holiday::find($id);
    	return view('backend.holiday.edit')->with(compact('index'));
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
            'name' => 'required',
            'date' => 'required',
            'type' => 'required',
        ]);

    	$index = Holiday::find($id);

        $index->name = $request->name;
        $index->date = date('Y-m-d', strtotime($request->date));
        $index->type = $request->type;
        
        $index->save();

        Session::flash('success', 'Data Has Been Updated');
    	return redirect()->route('admin.holiday');
    }

    public function delete(Request $request)
    {
    	Holiday::destroy($request->id);

        Session::flash('success', 'Data Has Been Deleted');
    	return redirect()->route('admin.holiday');
    }

    public function action(Request $request)
    {
    	if($request->action == 'delete')
    	{
    		Holiday::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
    	}
    	else if($request->action == 'enable')
    	{
    		$index = Holiday::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
    	}
    	else if($request->action == 'disable')
    	{
    		$index = Holiday::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
    	}
    	
    	return redirect()->route('admin.holiday');
    }

    public function active($id, $action)
    {
        $index = Holiday::find($id);

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

        return redirect()->route('admin.holiday');
    }
}
