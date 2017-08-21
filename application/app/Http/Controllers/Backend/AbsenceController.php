<?php
namespace App\Http\Controllers\Backend;

use App\Absence;
use App\AbsenceEmployee;
use App\AbsenceEmployeeDetail;
use App\JobTitle;
use App\Employee;
use App\Holiday;
use App\Dayoff;
use App\Overtime;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Facades\Input;
use Excel;
use File;

class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
	{
        $index = Absence::all();

    	return view('backend.absence.index')->with(compact('index'));
	}

    public function employee($id, Request $request)
    {
        $index = AbsenceEmployee::where('id_absence', $id)->get();
        $absence = Absence::find($id);

        return view('backend.absence.employee')->with(compact('index', 'absence'));
    }

    public function employeeDetail($id, Request $request)
    {
        $index = AbsenceEmployeeDetail::where('id_absence_employee', $id)->get();
        
        $absenceEmployee = AbsenceEmployee::find($id);
        
        // $start = $absenceEmployee->absence->date_start;

        // while($start <= $absenceEmployee->absence->date_end)
        // {
        //     $date[] = $start;
        //     $start = date('Y-m-d', strtotime($start . ' +1 day'));
        // }

        // $holiday = Holiday::whereIn('date', $date)->get();
        // $dayoff  = Dayoff::whereIn('start_dayoff', $date)->where('id_employee', $absenceEmployee->employee->id)->get();
        
        // $shift   = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
        //     ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
        //     ->join('shift_detail', 'shift_detail.id_shift', '=', 'attendance.id_shift')
        //     ->where('employee.id', $absenceEmployee->employee->id)
        //     ->select('day', 'shift_in', 'shift_out')
        //     ->get();

        // $overtime = Overtime::where('id_employee', $absenceEmployee->employee->id)->get();

        // $jobOvertime = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
        //     ->where('employee.id', $absenceEmployee->employee->id)
        //     ->select('book_overtime', 'min_overtime')
        //     ->first();

        // return view('backend.absence.employeeDetail')->with(compact('index', 'absenceEmployee', 'date', 'holiday', 'dayoff', 'shift', 'overtime', 'jobOvertime'));
        return view('backend.absence.employeeDetail')->with(compact('index', 'absenceEmployee'));
    }

	public function create(Request $request)
    {
        $jobTitle = JobTitle::all();
        $employee = Employee::all();

    	return view('backend.absence.create')->with(compact('jobTitle', 'employee'));
    }

	public function store(Request $request)
    {
        // return $request->all();

    	$this->validate($request, [
            'name' => 'required',
            'date' => 'required',
        ]);

        $periode = explode(' - ', $request->date);

        $start_periode = $periode[0];
        $end_periode = $periode[1];

        // get data from excel
        $data = '';
        if ($request->hasFile('excel'))
        {
            $data = Excel::load($request->file('excel')->getRealPath() , function($reader) {})->get();
            // return $data;
        }

        if(!empty($data))
        {
            // $absence = new Absence;

            // $absence->name = $request->name;
            // $absence->date_start = date('Y-m-d', strtotime($start_periode));
            // $absence->date_end = date('Y-m-d', strtotime($end_periode));

            // $absence->save();

            // grouping by sorted no ac and insert into AbsenceEmployee
            $return = '';
            $init_machine = 0;
            foreach ($data as $list) {
                // $return .= $list->nama . '<br/>';
                // if($init_machine != $list['ac_no.'])
                // {
                //     $init_machine = $list['ac_no.'];
                //     $insert[] = ['id_absence' => $absence->id, 'id_machine' => (int)$init_machine];
                // }

                if($init_machine != $list['no._id'])
                {
                    $init_machine = $list['no._id'];
                    $insert[] = ['id_absence' => 1, 'id_machine' => (int)$init_machine];
                }
            }
            return $insert;
            // insert into AbsenceEmployeeDetail

            if(!empty($insert)){
                AbsenceEmployee::insert($insert);

                $absenceEmployee = AbsenceEmployee::where('id_absence', $absence->id)->get();

                foreach ($absenceEmployee as $list) {

                    $start = $list->absence->date_start;

                    while($start <= $list->absence->date_end)
                    {
                        $date[] = $start;
                        $start = date('Y-m-d', strtotime($start . ' +1 day'));
                    }

                    
                    $dayoff  = Dayoff::whereIn('start_dayoff', $date)->where('id_employee', $list->employee->id)->get();
                    
                    $shift   = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
                        ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
                        ->join('shift_detail', 'shift_detail.id_shift', '=', 'attendance.id_shift')
                        ->where('employee.id', $list->employee->id)
                        ->select('day', 'shift_in', 'shift_out')
                        ->get();

                    $overtime = Overtime::where('id_employee', $list->employee->id)->get();

                    $jobOvertime = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
                        ->where('employee.id', $list->employee->id)
                        ->select('book_overtime', 'min_overtime')
                        ->first();


                    foreach ($data as $list2) {
                        $date_explode = explode('/', $list2['date']);

                        $format_date = $date_explode[0];
                        $format_month = $date_explode[1];
                        $format_year = $date_explode[2];

                        $format_date_php = $format_year.'-'.$format_month.'-'.$format_date;

                        $clock_in = $list2['clock_in_1'];

                        if($list2['clock_out_5'])
                        {
                            $clock_out = $list2['clock_out_5'];
                        }
                        else if($list2['clock_out_4'])
                        {   $clock_out = $list2['clock_out_4'];

                        }
                        else if($list2['clock_out_3'])
                        {
                            $clock_out = $list2['clock_out_3'];
                        }
                        else if($list2['clock_out_2'])
                        {
                            $clock_out = $list2['clock_out_2'];
                        }
                        else if($list2['clock_out_1'])
                        {
                            $clock_out = $list2['clock_out_1'];
                        }
                        else
                        {
                            $clock_out = '';
                        }

                        foreach($shift as $list2)
                        {
                            if (date('w', strtotime($date)) == $list2->day)
                            {
                                $day = $list2->day;
                                $shift_in = $list2->shift_in;
                                $shift_out = $list2->shift_out;
                                break;
                            }
                        }

                        if($list->id_machine == $list2['ac_no.'])
                        {
                            $insert2[] = [
                                'id_absence_employee' => $list->id,
                                'schedule_in' => $list->schedule_in,
                                'schedule_out' => $list->schedule_out,
                                'time_in' => $clock_in ? $format_date_php.' '.$clock_in : '',
                                'time_out' => $clock_out ? $format_date_php.' '.$clock_out : '',
                                'status' => $list->status,
                                'status_note' => $list->status_note,
                                'gaji' => $list->gaji,
                                'gaji_pokok' => $list->gaji_pokok,
                                'time_overtime' => $list->time_overtime,
                                'total_overtime' => $list->total_overtime,
                                'bonus_overtime' => $list->bonus_overtime,
                                'total_late' => $list->total_late,
                                'fine_late' => $list->fine_late,
                            ];
                        }
                    }
                }

                AbsenceEmployeeDetail::insert($insert2);
            }

            
        }

        Session::flash('success', 'Data Has Been Added');
    	return redirect()->route('admin.absence');
    }

    public function edit($id)
    {
        $index = Absence::find($id);

    	return view('backend.absence.edit')->with(compact('index'));
    }

    public function update($id, Request $request)
    {
    	$this->validate($request, [
            'name' => 'required',
            'date' => 'required',
        ]);

        $periode = explode(' - ', $request->date);

        $start_periode = $periode[0];
        $end_periode = $periode[1];

        $index = Absence::find($id);

        $index->name = $request->name;
        $index->date_start = date('Y-m-d', strtotime($start_periode));
        $index->date_end = date('Y-m-d', strtotime($end_periode));

        $index->save();

        Session::flash('success', 'Data Has Been Updated');
    	return redirect()->route('admin.absence');
    }

    public function delete($id, Request $request)
    {
        if(isset($request->type) && $request->type == 'absenceEmployee')
        {
            AbsenceEmployee::destroy($id);
        }
        else if(isset($request->type) && $request->type == 'absenceEmployeeDetail')
        {
            AbsenceEmployeeDetail::destroy($id);
        }
        else
        {
            Absence::destroy($id);
        }

        Session::flash('success', 'Data Has Been Deleted');
    	return redirect()->route('admin.absence');
    }

    public function action(Request $request)
    {
    	if(isset($request->id))
    	{
    		if($request->action == 'delete')
	    	{
                if(isset($request->type) && $request->type == 'absenceEmployee')
                {
                    AbsenceEmployee::destroy($request->id);
                }
                else if(isset($request->type) && $request->type == 'absenceEmployeeDetail')
                {
                    AbsenceEmployeeDetail::destroy($request->id);
                }
                else
                {
                    Absence::destroy($request->id);
                }
	    		
	            Session::flash('success', 'Data Selected Has Been Deleted');
	    	}
	    	else if($request->action == 'enable')
	    	{
	    		$index = Absence::whereIn('id', $request->id)->update(['active' => 1]);
	            Session::flash('success', 'Data Selected Has Been Actived');
	    	}
	    	else if($request->action == 'disable')
	    	{
	    		$index = Absence::whereIn('id', $request->id)->update(['active' => 0]);
	            Session::flash('success', 'Data Selected Has Been Inactived');
	    	}
    	}
    	
    	return redirect()->route('admin.absence');
    }

    public function active($id, $action)
    {
        $index = Absence::find($id);

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

        return redirect()->route('admin.absence');
    }
}
