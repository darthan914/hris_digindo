<?php

namespace App\Http\Controllers\Backend;

use App\Employee;
use App\Holiday;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Session;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
	{
		$f_year = 0;
    	if(isset($request->f_year))
    	{
    		$f_year = $request->f_year;
    	}

    	$year = Holiday::groupBy(DB::raw('YEAR(date)'))->select(DB::raw('YEAR(date) as year'))->get();

		$index = Holiday::orderBy('date', 'ASC');
		if($f_year != 0)
        {
        	$index = $index->whereYear('date', $f_year);
        }
        $index = $index->get();

    	return view('backend.holiday.index')->with(compact('index', 'year', 'f_year'));
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

    public function delete($id)
    {
    	Holiday::destroy($id);

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
