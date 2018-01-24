<?php

namespace App\Http\Controllers\Backend;

use App\Shift;
use App\ShiftDetail;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use File;
use Datatables;

use App\Http\Controllers\Controller;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
	{
		
    	return view('backend.shift.index')->with(compact('request'));
	}

    public function datatables(Request $request)
    {
        $index = Shift::all();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';
            if (Auth::user()->can('view-shift'))
            {
                $html .= '
                    <a href="' . route('admin.shift.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-eye"></i></a>
                ';
            }
            
            if (Auth::user()->can('delete-shift'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-shift" data-toggle="modal" data-target="#delete-shift" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
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
    	return view('backend.shift.create');
    }

	public function store(Request $request)
    {
    	$this->validate($request, [
            'code'          => 'required|unique:shift,code',
			'name'          => 'required',
            'day_per_month' => 'required|integer',
        ]);

    	$index = new Shift;

        $index->code            = $request->code;
		$index->name            = $request->name;
		$index->work_in_holiday = isset($request->work_in_holiday) ? 1 : 0;
        $index->day_per_month   = $request->day_per_month;

        $index->save();

        Session::flash('success', 'Data Berhasil ditambah');
    	return redirect()->route('admin.shift.edit', $index->id);
    }

    public function edit($id)
    {
        $index = Shift::find($id);

        return view('backend.shift.edit')->with(compact('index', 'detail', 'hari'));
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
            'code'          => 'required|unique:shift,code,'.$id,
			'name'          => 'required',
            'day_per_month' => 'required|integer',
        ]);

    	$index = Shift::find($id);

        $index->code            = $request->code;
		$index->name            = $request->name;
		$index->work_in_holiday = isset($request->work_in_holiday) ? 1 : 0;
        $index->day_per_month   = $request->day_per_month;

        $index->save();

        Session::flash('success', 'Data Berhasil diupdate');
    	return redirect()->back();
    }

    public function delete(Request $request)
    {
    	Shift::destroy($request->id);

        Session::flash('success', 'Data Has Been Deleted');
    	return redirect()->route('admin.shift');
    }

    public function action(Request $request)
    {
    	if($request->action == 'delete')
    	{
    		Shift::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
    	}
    	
    	return redirect()->route('admin.shift');
    }

    public function datatablesDetail($id)
    {
        $index = ShiftDetail::where('id_shift', $id)->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';
            if (Auth::user()->can('edit-shift'))
            {
                $html .= '
                    <a href="' . route('admin.shift.editDetail', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }
            
            if (Auth::user()->can('edit-shift'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-shiftDetail" data-toggle="modal" data-target="#delete-shiftDetail" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
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

        $datatables->editColumn('day', function ($index) {
            $day = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return $day[$index->day];
        });

        $datatables = $datatables->make(true);
        return $datatables;
    }

    public function createDetail($id)
    {
        $shift = Shift::find($id);
        return view('backend.shift.detail.create')->with(compact('shift'));
    }

    public function storeDetail($id, Request $request)
    {
        $this->validate($request, [
            'day' => 'required|integer',
            'shift_in' => 'required',
            'shift_out' => 'required',
        ]);

        $index = new ShiftDetail;

        $index->id_shift = $id;
        $index->day = $request->day;
        $index->shift_in = date('H:i:s', strtotime($request->shift_in));
        $index->shift_out = date('H:i:s', strtotime($request->shift_out));

        $index->save();

        Session::flash('success', 'Data Berhasil ditambah');
        return redirect()->route('admin.shift.edit', ['id' => $id]);
    }

    public function editDetail($id)
    {
        $index = ShiftDetail::find($id);
        $shift = Shift::where('id', $index->id_shift)->first();

        return view('backend.shift.detail.edit')->with(compact('index', 'shift'));
    }

    public function updateDetail($id, Request $request)
    {
        $this->validate($request, [
            'day' => 'required|integer',
            'shift_in' => 'required',
            'shift_out' => 'required',
        ]);

        $index = ShiftDetail::find($id);

        $index->day = $request->day;
        $index->shift_in = date('H:i:s', strtotime($request->shift_in));
        $index->shift_out = date('H:i:s', strtotime($request->shift_out));

        $index->save();

        Session::flash('success', 'Data Berhasil diupdate');
        return redirect()->route('admin.shift.edit', ['id' => $index->id_shift]);
    }

    public function deleteDetail(Request $request)
    {
        ShiftDetail::destroy($request->id);

        Session::flash('success', 'Data Has Been Deleted');
        return redirect::back();
    }

    public function actionDetail(Request $request)
    {
        if($request->action == 'delete')
        {
            ShiftDetail::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
        }
        else if($request->action == 'enable')
        {
            $index = ShiftDetail::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
        }
        else if($request->action == 'disable')
        {
            $index = ShiftDetail::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
        }
        
        return redirect::back();
    }

    public function activeDetail($id, $action)
    {
        $index = ShiftDetail::find($id);

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

        return redirect::back();
    }
}
