<?php

namespace App\Http\Controllers\Backend;

use App\Dayoff;
use App\Holiday;
use App\Employee;
use App\Shift;
use App\ShiftDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Datatables;
use Illuminate\Support\Facades\Input;

class DayoffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
    	// Filter Data
        $tab = $this->filter($request->tab, 'index');
    	
    	$employee = Employee::all();
    	$year     = Dayoff::groupBy(DB::raw('YEAR(start_dayoff)'))->select(DB::raw('YEAR(start_dayoff) as year'))->get();

        $this_year = $this->filter($request->f_year, date('Y'));

        
        $holidayDayoff     = Holiday::where('type', 'cuti')->whereYear('date', $this_year )->count();
        $lastHolidayDayoff = Holiday::where('type', 'cuti')->whereYear('date', ($this_year)-1 )->count();

        return view('backend.dayoff.index')->with(compact('tab', 'employee', 'year', 'holidayDayoff', 'lastHolidayDayoff', 'request'));
    }

    public function datatables(Request $request)
    {
        $f_id_employee = $this->filter($request->f_id_employee);
        $f_year        = $this->filter($request->f_year);

        // index Dayoff
        $index = Dayoff::join('employee', 'employee.id', '=', 'dayoff.id_employee')
            ->join('shift', 'shift.id', '=', 'employee.id_shift')
            ->select(DB::raw('dayoff.*, employee.name, employee.job_title, shift.name as shift, id_shift'))
            ->orderBy('id', 'DESC');

        if($f_id_employee != '')
        {
            $index = $index->where('id_employee', $f_id_employee);
        }
        if($f_year != '')
        {
            $index = $index->whereYear('start_dayoff', $f_year);
        }

        $index = $index->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('edit-dayoff'))
            {
                $html .= '
                    <a href="' . route('admin.dayoff.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('delete-dayoff'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-dayoff" data-toggle="modal" data-target="#delete-dayoff" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            if(Auth::user()->can('confirm-dayoff') && $index->check_leader == 0)
            {
                $html .= '
                    <button class="btn btn-xs btn-success confirm-dayoff" data-toggle="modal" data-target="#confirm-dayoff" data-id="'.$index->id.'"><i class="fa fa-check"></i></button>
                ';
            }

            if(Auth::user()->can('confirm-dayoff') && $index->check_leader == 1)
            {
                $html .= '
                    <button class="btn btn-xs btn-warning cancel-dayoff" data-toggle="modal" data-target="#cancel-dayoff" data-id="'.$index->id.'"><i class="fa fa-times"></i></button>
                ';
            }

            return $html;
        });

        $datatables->addColumn('check', function ($index) {
            $html = '';

            $html .= '
                <input type="checkbox" class="check-index" value="' . $index->id . '" name="id[]" form="action">
            ';

            return $html;
        });

        $datatables->editColumn('shift', function ($index) {
            $html = '';

            if(Auth::user()->can('view-shift'))
            {
                $html .= '
                    <a href="'. route('admin.shift.edit', ['id' => $index->id_shift]) .'">'. $index->shift .'</a>
                ';
            }
            else
            {
                $html .= $index->shift;
            }
                
            return $html;
        });

        $datatables->editColumn('date', function ($index) {
            return date('d/m/Y', strtotime($index->date));
        });

        $datatables->editColumn('start_dayoff', function ($index) {
            return date('d/m/Y', strtotime($index->start_dayoff));
        });

        $datatables->editColumn('end_dayoff', function ($index) {
            return date('d/m/Y', strtotime($index->end_dayoff));
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

        $datatables = $datatables->make(true);
        return $datatables;
    }

    public function datatablesRemain(Request $request)
    {
        $f_year = $this->filter($request->f_year, date('Y'));

        $holidayDayoff     = Holiday::where('type', 'cuti')->whereYear('date', $f_year )->count();
        $lastHolidayDayoff = Holiday::where('type', 'cuti')->whereYear('date', ($f_year)-1 )->count();

        $query_this_year = "
            (SELECT `id_employee`, SUM(`total_dayoff`) AS `sum_total_dayoff` FROM `dayoff` WHERE YEAR(`start_dayoff`) = ".($f_year ? $f_year : date('Y'))." AND `check_leader` = 1 GROUP BY `id_employee`) AS this_year
        ";

        $query_last_year = "
            (SELECT `id_employee`, SUM(`total_dayoff`) AS `sum_total_dayoff` FROM `dayoff` WHERE YEAR(`start_dayoff`) = ".($f_year ? $f_year-1 : date('Y')-1) ." AND `check_leader` = 1 GROUP BY `id_employee`) AS last_year
        ";

        // total dayoff remain
        $index = Employee::whereYear('date_resign', '>=' ,$f_year)
            ->orwhereNull('date_resign')
            ->leftJoin(DB::raw($query_this_year), 'employee.id', 'this_year.id_employee')
            ->leftJoin(DB::raw($query_last_year), 'employee.id', 'last_year.id_employee')
            ->select('employee.*', 'this_year.sum_total_dayoff as dayoff_this_year', 'last_year.sum_total_dayoff as dayoff_last_year')
            ->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) use ($f_year) {
            $html = '<a href="'. route('admin.dayoff', ['f_id_employee' => $index->id, 'f_year' => $f_year ? $f_year : date('Y')]) .'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>';

            return $html;
        });

        $datatables->addColumn('check', function ($index) {
            $html = '';

            $html .= '
                <input type="checkbox" class="check-remain" value="' . $index->id . '" name="id[]" form="action-remain">
            ';

            return $html;
        });


        $datatables->addColumn('dayoff_holiday', function ($index) use ($holidayDayoff){
            return $holidayDayoff;
        });

        $datatables->addColumn('dayoff_remain_last_year', function ($index) use ($lastHolidayDayoff){
            return (12 - $index->dayoff_last_year - $lastHolidayDayoff < 0 ? 12 - $index->dayoff_last_year - $lastHolidayDayoff : 0);
        });

        $datatables->addColumn('dayoff_remain', function ($index) use ($holidayDayoff, $lastHolidayDayoff){
            $dayoff_remain_last_year = (12 - $index->dayoff_last_year - $lastHolidayDayoff < 0 ? 12 - $index->dayoff_last_year - $lastHolidayDayoff : 0);

            return 12 - $index->dayoff_this_year - $holidayDayoff + $dayoff_remain_last_year;
        });

        $datatables = $datatables->make(true);
        return $datatables;
    }

    public function create()
    {
    	$employee = Employee::all();

        return view('backend.dayoff.create')->with(compact('employee'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'id_employee' => 'required',
            'date'        => 'required',
            'dayoff'      => 'required',
            'type'        => 'required',
            'note'        => 'required',
        ]);

        $dayoff = explode(' - ', $request->dayoff);

        $start_dayoff = $dayoff[0];
        $end_dayoff = $dayoff[1];

        $total_dayoff = 0;

        $employee = Employee::where('id', $request->id_employee)->first();

        if($request->type == 'cuti')
        {
            while(1)
            {
                if(Holiday::whereDate('date', date('Y-m-d', strtotime($start_dayoff)))->count() == 0)
                {

                    if(Employee::join('shift', 'shift.id', 'employee.id_shift')->join('shift_detail', 'shift.id', 'shift_detail.id_shift')->where('employee.id', $employee->id)->where('shift_detail.day', date('w', strtotime($start_dayoff)))->count() > 0)
                    {
                        $total_dayoff++;
                    }
                        
                }
                

                if($start_dayoff >= $end_dayoff)
                {
                    break;
                }
                else
                {
                    $start_dayoff = date('d F Y', strtotime($start_dayoff . ' +1 day'));
                }
                
            }
        }

        if(isset($request->half_day))
        {
            $total_dayoff = 0.5;
        }

        $totalHolidayDayoff = Holiday::where('type', 'cuti')
            ->whereYear('date', date('Y', strtotime($dayoff[0])))
            ->groupBy(DB::raw('YEAR(date)'))
            ->count();

        $totalEmployeeDayoff = Dayoff::join('employee', 'employee.id', 'dayoff.id_employee')
            ->where('type', 'cuti')
            ->where('employee.id', $request->id_employee)
            ->whereYear('start_dayoff', date('Y', strtotime($dayoff[0])))
            ->groupBy(DB::raw('employee.name, YEAR(start_dayoff)'))
            ->sum('total_dayoff');

        $count_dayoff = 12;
        if(date('Y', strtotime($employee->date_join)) == date('Y'))
        {
            $count_dayoff = $count_dayoff - (date('n', strtotime($employee->date_join)) - 1) - 3;
            
            if($count_dayoff < 0)
            {
                $count_dayoff = 0;
            }
        }

        $validator = Validator::make($request->all(), [
            'id_employee' => 'required',
            'date'        => 'required',
            'dayoff'      => 'required',
            'type'        => 'required',
            'note'        => 'required',
        ]);

        $validator->after(function ($validator) use ($request, $total_dayoff, $totalHolidayDayoff, $totalEmployeeDayoff, $count_dayoff
        ) {

            if($total_dayoff > 1 && isset($request->half_day))
            {
                $validator->errors()->add('dayoff', 'Cuti Setengah hari untuk 1 hari');
            }

            if($total_dayoff == 0 && $request->type == 'cuti') {
                $validator->errors()->add('dayoff', 'Cuti tidak tersedia');
            }

            // if($total_dayoff > 4) {
            //     $validator->errors()->add('dayoff', 'Maksimal cuti 4 hari');
            // }

            // if(($totalHolidayDayoff + $totalEmployeeDayoff + $total_dayoff) >= $count_dayoff)
            // {
            //     $validator->errors()->add('dayoff', 'Masa cuti anda habis');
            // }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        

        $index = new Dayoff;

        $index->id_employee  = $request->id_employee;
        $index->date         = date('Y-m-d', strtotime($request->date));
		$index->total_dayoff = $total_dayoff;
		$index->start_dayoff = date('Y-m-d', strtotime($dayoff[0]));
		$index->end_dayoff   = date('Y-m-d', strtotime($dayoff[1]));
        $index->half_day     = isset($request->half_day) ? 1 : 0;
		$index->type         = $request->type;
		$index->note         = $request->note;

        $index->save();

        Session::flash('success', 'Data berhasil ditambah');
        return redirect()->route('admin.dayoff');
    }

    public function edit($id)
    {
        $index    = Dayoff::find($id);
        $employee = Employee::all();

        return view('backend.dayoff.edit')->with(compact('index', 'employee'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'id_employee' => 'required',
            'date'        => 'required',
            'dayoff'      => 'required',
            'type'        => 'required',
            'note'        => 'required',
        ]);

        $dayoff = explode(' - ', $request->dayoff);

        $start_dayoff = $dayoff[0];
        $end_dayoff = $dayoff[1];

        $total_dayoff = 0;

        $employee = Employee::where('id', $request->id_employee)->first();

        if($request->type == 'cuti')
        {
            while(1)
            {
                if(Holiday::whereDate('date', date('Y-m-d', strtotime($start_dayoff)))->count() == 0)
                {

                    if(Employee::join('shift', 'shift.id', 'employee.id_shift')->join('shift_detail', 'shift.id', 'shift_detail.id_shift')->where('employee.id', $employee->id)->where('shift_detail.day', date('w', strtotime($start_dayoff)))->count() > 0)
                    {
                        $total_dayoff++;
                    }
                        
                }
                

                if($start_dayoff >= $end_dayoff)
                {
                    break;
                }
                else
                {
                    $start_dayoff = date('d F Y', strtotime($start_dayoff . ' +1 day'));
                }
                
            }
        }

        if(isset($request->half_day))
        {
            $total_dayoff = 0.5;
        }

        $totalHolidayDayoff = Holiday::where('type', 'cuti')
            ->whereYear('date', date('Y', strtotime($dayoff[0])))
            ->groupBy(DB::raw('YEAR(date)'))
            ->count();

        $totalEmployeeDayoff = Dayoff::join('employee', 'employee.id', 'dayoff.id_employee')
            ->where('type', 'cuti')
            ->where('employee.id', $request->id_employee)
            ->whereYear('start_dayoff', date('Y', strtotime($dayoff[0])))
            ->groupBy(DB::raw('employee.name, YEAR(start_dayoff)'))
            ->sum('total_dayoff');

        $count_dayoff = 12;
        if(date('Y', strtotime($employee->date_join)) == date('Y'))
        {
            $count_dayoff = $count_dayoff - (date('n', strtotime($employee->date_join)) - 1) - 3;
            
            if($count_dayoff < 0)
            {
                $count_dayoff = 0;
            }
        }

        $validator = Validator::make($request->all(), [
            'id_employee' => 'required',
            'date'        => 'required',
            'dayoff'      => 'required',
            'type'        => 'required',
            'note'        => 'required',
        ]);

        $validator->after(function ($validator) use ($request, $total_dayoff, $totalHolidayDayoff, $totalEmployeeDayoff, $count_dayoff
        ) {

            if($total_dayoff > 1 && isset($request->half_day))
            {
                $validator->errors()->add('dayoff', 'Cuti Setengah hari untuk 1 hari');
            }

            if($total_dayoff == 0) {
                $validator->errors()->add('dayoff', 'Cuti tidak tersedia');
            }

            // if($total_dayoff > 4) {
            //     $validator->errors()->add('dayoff', 'Maksimal cuti 4 hari');
            // }

            // if(($totalHolidayDayoff + $totalEmployeeDayoff + $total_dayoff) >= $count_dayoff)
            // {
            //     $validator->errors()->add('dayoff', 'Masa cuti anda habis');
            // }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        

        $index = Dayoff::find($id);

        $index->id_employee  = $request->id_employee;
        $index->date         = date('Y-m-d', strtotime($request->date));
		$index->total_dayoff = $total_dayoff;
		$index->start_dayoff = date('Y-m-d', strtotime($dayoff[0]));
		$index->end_dayoff   = date('Y-m-d', strtotime($dayoff[1]));
        $index->half_day     = isset($request->half_day) ? 1 : 0;
		$index->type         = $request->type;
		$index->note         = $request->note;

        $index->save();

        Session::flash('success', 'Data berhasil diupdate');
        return redirect()->route('admin.dayoff');
    }

    public function delete(Request $request)
    {
        Dayoff::destroy($request->id);

        Session::flash('success', 'Data berhasil dihapus');
        return redirect()->route('admin.dayoff');
    }

    public function action(Request $request)
    {
        if ($request->action == 'delete') {
            Dayoff::destroy($request->id);
            Session::flash('success', 'Data dipilih berhasil dihapus');
        } else if ($request->action == 'enable') {
            $index = Dayoff::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data dipilih berhasil diaktifkan');
        } else if ($request->action == 'disable') {
            $index = Dayoff::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data dipilih berhasil dinon aktifkan');
        }

        return redirect()->route('admin.dayoff');
    }

    public function active($id, $action)
    {
        $index = Dayoff::find($id);

        $index->active = $action;

        $index->save();

        if ($action == 1) {
            Session::flash('success', 'Data Has Been Actived');
        } else {
            Session::flash('success', 'Data Has Been Inactived');
        }

        return redirect()->route('admin.dayoff');
    }

    public function confirm(Request $request)
    {
        $index = Dayoff::find($request->id);

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
        return redirect()->route('admin.dayoff');
    }
}
