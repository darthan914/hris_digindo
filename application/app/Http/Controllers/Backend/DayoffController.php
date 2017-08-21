<?php

namespace App\Http\Controllers\Backend;

use App\Dayoff;
use App\Holiday;
use App\Employee;
use App\Attendance;
use App\ShiftDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Session;
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
    	$tab = 'index';
    	if(isset($request->tab))
    	{
    		$tab = $request->tab;
    	}

    	$f_id_employee = 0;
    	if(isset($request->f_id_employee))
    	{
    		$f_id_employee = $request->f_id_employee;
    	}
    	$employee = Employee::all();

    	$f_year = 0;
    	if(isset($request->f_year))
    	{
    		$f_year = $request->f_year;
    	}
    	$year = Dayoff::groupBy(DB::raw('YEAR(start_dayoff)'))->select(DB::raw('YEAR(start_dayoff) as year'))->get();


    	// index Dayoff
        $index = Dayoff::join('employee', 'employee.id', '=', 'dayoff.id_employee')
            ->join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
            ->join('shift', 'shift.id', '=', 'attendance.id_shift')
        	->select(DB::raw('dayoff.*, employee.name, job_title.name as position, shift.name as shift, id_shift'))
        	->orderBy('id', 'DESC');

        if($f_id_employee != 0)
        {
        	$index = $index->where('id_employee', $f_id_employee);
        }
        if($f_year != 0)
        {
            $index = $index->whereYear('start_dayoff', $f_year);
        }

        $index = $index->get();

        $this_year = date('Y');
        if($f_year != 0)
        {
            $this_year = $f_year;
        }

        // total left dayoff
        $totalDayoff = Employee::whereYear('date_resign', '>=' ,$this_year)
            ->orwhereNull('date_resign')
            ->get();

        $holidayDayoff     = Holiday::where('type', 'cuti')->whereYear('date', $this_year )->count();
        $lastHolidayDayoff = Holiday::where('type', 'cuti')->whereYear('date', ($this_year)-1 )->count();

        return view('backend.dayoff.index')->with(compact('index', 'totalDayoff', 'tab', 'employee', 'f_id_employee', 'year', 'f_year', 'holidayDayoff', 'lastHolidayDayoff'));
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
                    if(Attendance::join('shift', 'shift.id', '=', 'attendance.id_shift')->where('attendance.id_job_title', $employee->id_job_title))
                    {
                        if(Attendance::join('shift_detail', 'shift_detail.id_shift', '=', 'attendance.id_shift')->where('attendance.id_job_title', $employee->id_job_title)->where('shift_detail.day', date('w', strtotime($start_dayoff)))->count() > 0)
                        {
                            $total_dayoff++;
                        }
                    }
                    else
                    {
                        if(date('w', strtotime($start_dayoff)) != 0 && date('w', strtotime($start_dayoff)) != 6)
                        {
                            $total_dayoff++;
                        }
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

        if($total_dayoff > 1 && isset($request->half_day))
        {
            Session::flash('failed', 'Cuti Setengah hari untuk 1 hari');
            return redirect::back()->with(Input::flash());
        }

        if(isset($request->half_day))
        {
            $total_dayoff = 0.5;
        }

        if($total_dayoff == 0)
        {
            Session::flash('info', 'Cuti tidak tersedia');
            return redirect()->route('admin.dayoff');
        }

        if($total_dayoff > 4)
        {
        	Session::flash('failed', 'Maksimal cuti 4 hari');
        	return redirect::back()->with(Input::flash());
        }

        $totalHolidayDayoff = Holiday::where('type', 'cuti')
            ->whereYear('date', date('Y', strtotime($dayoff[0])))
            ->groupBy(DB::raw('YEAR(date)'))
            ->count();

        $totalEmployeeDayoff = Dayoff::join('employee', 'employee.id', '=', 'dayoff.id_employee')
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

        // return $totalHolidayDayoff + $totalEmployeeDayoff + $total_dayoff;

        if(($totalHolidayDayoff + $totalEmployeeDayoff + $total_dayoff) >= $count_dayoff)
        {
            Session::flash('failed', 'Masa cuti anda habis');
            return redirect::back()->with(Input::flash());
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

        Session::flash('success', 'Data Has Been Added');
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
                    if(Attendance::join('shift', 'shift.id', '=', 'attendance.id_shift')->where('attendance.id_job_title', $employee->id_job_title))
                    {
                        if(Attendance::join('shift_detail', 'shift_detail.id_shift', '=', 'attendance.id_shift')->where('attendance.id_job_title', $employee->id_job_title)->where('shift_detail.day', date('w', strtotime($start_dayoff)))->count() > 0)
                        {
                            // $jikaada++;
                            $total_dayoff++;
                        }
                    }
                    else
                    {
                        if(date('w', strtotime($start_dayoff)) != 0 && date('w', strtotime($start_dayoff)) != 6)
                        {
                            // $jikatidakada++;
                            $total_dayoff++;
                        }
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

        // return 'Jika ada : '. $jikaada . ', Jika tidak ada : ' . $jikatidakada;

        if($total_dayoff > 1 && isset($request->half_day))
        {
            Session::flash('failed', 'Cuti Setengah hari untuk 1 hari');
            return redirect::back()->with(Input::flash());
        }

        if(isset($request->half_day))
        {
            $total_dayoff = 0.5;
        }

        if($total_dayoff == 0)
        {
            Session::flash('info', 'Cuti tidak tersedia');
            return redirect()->route('admin.dayoff');
        }

        if($total_dayoff > 4)
        {
        	Session::flash('failed', 'Over Than 4 day');
        	return redirect::back()->with(Input::flash());
        }

        $totalHolidayDayoff = Holiday::where('type', 'cuti')
            ->whereYear('date', date('Y', strtotime($dayoff[0])))
            ->groupBy(DB::raw('YEAR(date)'))
            ->count();

        $totalEmployeeDayoff = Dayoff::join('employee', 'employee.id', '=', 'dayoff.id_employee')
            ->where('type', 'cuti')
            ->where('employee.id', $request->id_employee)
            ->where('dayoff.id', '<>' ,$id)
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

        // return $totalHolidayDayoff + $totalEmployeeDayoff + $total_dayoff;

        if(($totalHolidayDayoff + $totalEmployeeDayoff + $total_dayoff) >= $count_dayoff)
        {
            Session::flash('failed', 'Masa cuti anda habis');
            return redirect::back()->with(Input::flash());
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

        Session::flash('success', 'Data Has Been Updated');
        return redirect()->route('admin.dayoff');
    }

    public function delete($id)
    {
        Dayoff::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
        return redirect()->route('admin.dayoff');
    }

    public function action(Request $request)
    {
        if ($request->action == 'delete') {
            Dayoff::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
        } else if ($request->action == 'enable') {
            $index = Dayoff::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
        } else if ($request->action == 'disable') {
            $index = Dayoff::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
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
}
