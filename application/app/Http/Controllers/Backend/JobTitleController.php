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
    	return view('backend.jobTitle.index');
	}

    public function datatables(Request $request)
    {
        $index = JobTitle::select('*');

        $index = $index->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('edit-jobTitle'))
            {
                $html .= '
                
                    <a href="' . route('admin.jobTitle.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('delete-jobTitle'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-jobTitle" data-toggle="modal" data-target="#delete-jobTitle" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            return $html;
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

    	return redirect()->route('admin.jobTitle')->with('success', 'Data berhasil ditambah');
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

    	return redirect()->route('admin.jobTitle')->with('success', 'Data Berhasil diubah');
    }

    public function delete($id)
    {
    	JobTitle::destroy($id);

        return redirect()->back()->with('success', 'Data Berhasil dihapus');
    }

    public function action(Request $request)
    {
    	if($request->action == 'delete')
    	{
    		JobTitle::destroy($request->id);
            return redirect()->back()->with('success', 'Data berhasil dihapus');
    	}
    	
    	return redirect()->back()->with('failed', 'Akses ditolak');
    }
}
