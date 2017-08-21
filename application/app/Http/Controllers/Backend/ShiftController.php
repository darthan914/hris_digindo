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

use App\Http\Controllers\Controller;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
	{
		$index = Shift::all();

		$detail = ShiftDetail::orderBy('id', 'ASC');

    	return view('backend.shift.index')->with(compact('index', 'detail'));
	}

	public function create()
    {

    	return view('backend.shift.create');
    }

	public function store(Request $request)
    {
    	$this->validate($request, [
            'code' => 'required|unique:shift,code',
			'name' => 'required',
			'shift_in' => 'required',
			'shift_out' => 'required',
			'late' => 'required|integer',
        ]);

    	$index = new Shift;

        $index->code            = $request->code;
		$index->name            = $request->name;
		$index->shift_in        = date('H:i:s', strtotime($request->shift_in));
		$index->shift_out       = date('H:i:s', strtotime($request->shift_out));
		$index->work_in_holiday = isset($request->work_in_holiday) ? 1 : 0;
		$index->late            = $request->late;

        $index->save();

        $count = 0;
        $detail = [];
        if (isset($request->day)) {
            foreach ($request->day as $key) {
                $array = [
                    'id_shift'  => $index->id,
                    'day'       => $request->day[$count],
                    'shift_in'  => date('H:i:s', strtotime($request->shift_in)),
                    'shift_out' => date('H:i:s', strtotime($request->shift_out)),
                ];
                $count++;
                array_push($detail, $array);
            }
        }

        $shiftDetail = ShiftDetail::insert($detail);
        

        Session::flash('success', 'Data Has Been Added');
    	return redirect()->route('admin.shift');
    }

    public function edit($id)
    {
        $index = Shift::find($id);
        $detail = ShiftDetail::where('id_shift', $id)->orderBy('day', 'ASC')->get();
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('backend.shift.edit')->with(compact('index', 'detail', 'hari'));
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
            'code'      => 'required|unique:shift,code,'.$id,
			'name'      => 'required',
			'shift_in'  => 'required',
			'shift_out' => 'required',
			'late'      => 'required|integer',
        ]);

    	$index = Shift::find($id);

        $index->code            = $request->code;
		$index->name            = $request->name;
		$index->shift_in        = date('H:i:s', strtotime($request->shift_in));
		$index->shift_out       = date('H:i:s', strtotime($request->shift_out));
		$index->work_in_holiday = isset($request->work_in_holiday) ? 1 : 0;
		$index->late            = $request->late;

        $index->save();

        Session::flash('success', 'Data Has Been Updated');
    	return redirect()->route('admin.shift');
    }

    public function delete($id)
    {
    	Shift::destroy($id);

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
    	else if($request->action == 'enable')
    	{
    		$index = Shift::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
    	}
    	else if($request->action == 'disable')
    	{
    		$index = Shift::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
    	}
    	
    	return redirect()->route('admin.shift');
    }

    public function active($id, $action)
    {
        $index = Shift::find($id);

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

        return redirect()->route('admin.shift');
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

        Session::flash('success', 'Data Has Been Added');
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

        Session::flash('success', 'Data Has Been Updated');
        return redirect()->route('admin.shift.edit', ['id' => $index->id_shift]);
    }

    public function deleteDetail($id)
    {
        ShiftDetail::destroy($id);

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
