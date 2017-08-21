<?php

namespace App\Http\Controllers\Backend;

use App\ItemBorrowed;
use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use File;

use App\Http\Controllers\Controller;

class ItemBorrowedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
	{
		$index = ItemBorrowed::orderBy('id', 'DESC')
        	->get();

    	return view('backend.itemBorrowed.index')->with(compact('index'));
	}

	public function create()
    {
    	$employee = Employee::all();

    	return view('backend.itemBorrowed.create')->with(compact('employee'));
    }

	public function store(Request $request)
    {

    	$this->validate($request, [
            'id_employee' => 'required',
			'item'        => 'required',
        ]);

    	$index = new ItemBorrowed;

    	$index->id_employee = $request->id_employee;
		$index->item        = $request->item;

        $index->save();

        Session::flash('success', 'Data Has Been Added');
    	return redirect()->route('admin.itemBorrowed');
    }

    public function edit($id)
    {
    	$index = ItemBorrowed::find($id);

    	$employee = Employee::all();

    	return view('backend.itemBorrowed.edit')->with(compact('index', 'employee'));
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
            'id_employee' => 'required',
			'item'        => 'required',
        ]);

    	$index = ItemBorrowed::find($id);

        $index->id_employee = $request->id_employee;
		$index->item        = $request->item;
        
        $index->save();

        Session::flash('success', 'Data Has Been Updated');
    	return redirect()->route('admin.itemBorrowed');
    }

    public function delete($id)
    {
    	ItemBorrowed::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
    	return redirect()->route('admin.itemBorrowed');
    }

    public function action(Request $request)
    {
    	if($request->action == 'delete')
    	{
    		ItemBorrowed::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
    	}
    	else if($request->action == 'enable')
    	{
    		$index = ItemBorrowed::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
    	}
    	else if($request->action == 'disable')
    	{
    		$index = ItemBorrowed::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
    	}
    	
    	return redirect()->route('admin.itemBorrowed');
    }

    public function active($id, $action)
    {
        $index = ItemBorrowed::find($id);

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

        return redirect()->route('admin.itemBorrowed');
    }
}
