<?php

namespace App\Http\Controllers\Backend;

use App\JobTitle;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use File;

use App\Http\Controllers\Controller;

class JobTitleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
	{
		$index = JobTitle::all();

    	return view('backend.jobTitle.index')->with(compact('index'));
	}

	public function create()
    {

    	return view('backend.jobTitle.create');
    }

	public function store(Request $request)
    {

    	$this->validate($request, [
            'name' => 'required',
            'code' => 'required|unique:job_title,code',
            'per_day' => 'required|min:1|integer',
            'min_overtime' => 'required|integer',
        ]);

    	$index = new JobTitle;

        $index->name = $request->name;
        $index->code = $request->code;
        $index->per_day = $request->per_day;
        $index->book_overtime = isset($request->book_overtime) ? 1 : 0;
        $index->min_overtime = $request->min_overtime;
        
        $index->save();

        Session::flash('success', 'Data Has Been Added');
    	return redirect()->route('admin.jobTitle');
    }

    public function edit($id)
    {
    	$index = JobTitle::find($id);
    	return view('backend.jobTitle.edit')->with(compact('index'));
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
            'name' => 'required',
            'code' => 'required|unique:job_title,code,'.$id,
            'per_day' => 'required|min:1|integer',
            'min_overtime' => 'required|integer',
        ]);

    	$index = JobTitle::find($id);

        $index->name = $request->name;
        $index->code = $request->code;
        $index->per_day = $request->per_day;
        $index->book_overtime = isset($request->book_overtime) ? 1 : 0;
        $index->min_overtime = $request->min_overtime;
        
        $index->save();

        Session::flash('success', 'Data Has Been Updated');
    	return redirect()->route('admin.jobTitle');
    }

    public function delete($id)
    {
    	JobTitle::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
    	return redirect()->route('admin.jobTitle');
    }

    public function action(Request $request)
    {
    	if($request->action == 'delete')
    	{
    		JobTitle::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
    	}
    	else if($request->action == 'enable')
    	{
    		$index = JobTitle::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
    	}
    	else if($request->action == 'disable')
    	{
    		$index = JobTitle::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
    	}
    	
    	return redirect()->route('admin.jobTitle');
    }

    public function active($id, $action)
    {
        $index = JobTitle::find($id);

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

        return redirect()->route('admin.jobTitle');
    }
}
