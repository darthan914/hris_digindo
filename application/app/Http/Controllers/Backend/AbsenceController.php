<?php
namespace App\Http\Controllers\Backend;

use App\Absence;
use App\AbsenceEmployee;
use App\AbsenceEmployeeDetail;
use App\Dayoff;
use App\Employee;
use App\Holiday;
use App\Http\Controllers\Controller;
use App\JobTitle;
use App\Overtime;
use Excel;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
use Illuminate\Support\Facades\DB;

class OvertimePoint
{
    public $book_overtime;
    public $min_overtime;
    public $time_overtime;
    public $time_in;
    public $time_out;
    public $schedule_out;
    public $status;
    public $gaji;
}

abstract class AbsenceData
{
    public $date        = '0000-00-00';
    public $id_employee = 0;

    public $check_in    = NULL;
    public $check_out   = NULL;

    public function setDate(string $date)
    {
        $this->date = $date;
        return $this;
    }

    public function setIdEmployee(int $id_employee)
    {
        $this->id_employee = $id_employee;
        return $this;
    }

    public function setCheck(string $check_in = NULL, string $check_out = NULL)
    {
        $this->check_in  = $check_in;
        $this->check_out = $check_out;
        return $this;
    }
}

class HolidayData extends AbsenceData
{
    private $name       = '';
    private $type       = '';
    private $is_holiday = false;

    public function process()
    {
        $holiday = Holiday::where('date', $this->date)->first();

        if ($holiday) {
            $this->name       = $holiday->name;
            $this->type       = $holiday->type;
            $this->is_holiday = true;
        }
        else
        {
            $this->name = '';
            $this->type = '';
            $this->is_holiday =false;
        }

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isHoliday(): bool
    {
        return $this->is_holiday;
    }
}

class ScheduleData extends AbsenceData
{
    private $day         = NULL;
    private $shift_in    = NULL;
    private $shift_out   = NULL;
    private $is_schedule = false;


    public function process()
    {
        $shift_detail = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
            ->join('shift_detail', 'shift_detail.id_shift', '=', 'attendance.id_shift')
            ->where('employee.id', $this->id_employee)
            ->select('day', 'shift_in', 'shift_out')
            ->get();

        foreach ($shift_detail as $list) {
            if (date('w', strtotime($this->date)) == $list->day) {
                $this->day         = $list->day;
                $this->shift_in    = $list->shift_in;
                $this->shift_out   = $list->shift_out;
                $this->is_schedule = true;
                break;
            }
            else
            {
                $this->day         = NULL;
                $this->shift_in    = NULL;
                $this->shift_out   = NULL;
                $this->is_schedule = false;
            }
        }

        return $this;
    }

    public function getDay(): int
    {
        return $this->day;   
    }

    public function getTimeShiftIn()
    {
        return $this->shift_in;   
    }

    public function getTimeShiftOut()
    {
        return $this->shift_out;   
    }

    public function isSchedule(): bool
    {
        return $this->is_schedule;
    }
}

class DayoffData extends AbsenceData
{
    private $start_dayoff = '0000-00-00';
    private $end_dayoff   = '0000-00-00';
    private $total_dayoff = 0;
    private $note         = '';
    private $type         = '';
    private $is_dayoff    = false;

    public function process()
    {
        $dayoff = Dayoff::where('id_employee', $this->id_employee)->where('start_dayoff', '>', date('Y-m-d', strtotime('-3 months')))->get();

        foreach ($dayoff as $list) {
            if ($list->start_dayoff <= $this->date && $this->date <= $list->end_dayoff) {
                $this->start_dayoff = $list->start_dayoff;
                $this->end_dayoff   = $list->end_dayoff;
                $this->total_dayoff = $list->total_dayoff;
                $this->note         = $list->note;
                $this->type         = $list->type;
                $this->is_dayoff    = true;
                break;
            }
            else
            {
                $this->start_dayoff = '0000-00-00';
                $this->end_dayoff   = '0000-00-00';
                $this->total_dayoff = 0;
                $this->note         = '';
                $this->type         = '';
                $this->is_dayoff    = false;
            }
        }

        return $this;
    }

    public function getDateStartDayoff(): string
    {
        return $this->start_dayoff;   
    }

    public function getDateEndDayoff(): string
    {
        return $this->end_dayoff;   
    }

    public function getTotalDayoff(): int
    {
        return $this->total_dayoff;   
    }

    public function getNote(): string
    {
        return $this->note;   
    }

    public function getType(): string
    {
        return $this->type;   
    }

    public function isDayoff(): bool
    {
        return $this->is_dayoff;   
    }
}

class AttendanceData extends AbsenceData
{
    private $status      = '';
    private $status_note = '';
    private $gaji        = 0;

    private $late        = 0;
    private $minute_late = 0;
    private $point_late  = 0;

    private $holiday  = NULL;
    private $schedule = NULL;
    private $dayoff   = NULL;

    private $minute_overtime = 0;
    private $point_overtime  = 0;

    public function __construct()
    {
        $this->holiday  = new HolidayData;
        $this->schedule = new ScheduleData;
        $this->dayoff   = new DayoffData;
    }

    public function process()
    {
        $shift = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
            ->join('shift', 'shift.id', '=', 'attendance.id_shift')
            ->where('employee.id', $this->id_employee)
            ->select('late')
            ->first();

        if($shift)
        {
            $this->late = $shift->late;
        }
        else
        {
            $this->late = 0;
        }

        $holiday  = $this->holiday->setDate($this->date)->process();
        $schedule = $this->schedule->setDate($this->date)->setIdEmployee($this->id_employee)->process();
        $dayoff   = $this->dayoff->setDate($this->date)->setIdEmployee($this->id_employee)->process();

        $this->status_note = '';
        $this->gaji        = 0;

        if ($schedule->isSchedule() && $this->check_in && $this->check_out) 
        {
            $this->status = 'masuk';
            $this->gaji   = 1;

            if ($this->holiday->isHoliday()) {
                $this->status      = 'libur';
                $this->status_note = $this->holiday->getName();
                $this->gaji        = 1.5;
            }

        } 
        else if ($this->schedule->isSchedule() === 0 && $this->check_in && $this->check_out) 
        {
            $this->status = 'masuk';
            $this->gaji   = 1.5;
        } 
        else if ($this->holiday->isHoliday())
        {
            $status      = 'libur';
            $status_note = $this->holiday->getName();
            
            if ($this->schedule->isSchedule()) 
            {
                $this->gaji = 1;
            }
        } 
        else if ($this->dayoff->isDayoff()) 
        {
            $this->status      = $this->dayoff->getType();
            $this->status_note = $this->dayoff->getNote();
            $this->gaji        = 1;
        } 
        else if ($schedule->isSchedule() === 0)
        {
            $this->status = 'kosong';
            $this->gaji   = 0;
        }
        else
        {
            $this->status = 'alpa';
            $this->gaji   = -1;
        }


        if ($schedule->isSchedule() && strtotime($schedule->getTimeShiftIn()) < strtotime($this->check_in)) {
            $this->point_late   = strtotime($this->check_in) - strtotime($schedule->getTimeShiftIn());
            $this->minute_late  = (int) (($this->point_late / 60));
            $this->point_late   = (int) (($this->point_late / 60) / $this->late);
        }
        else
        {
            $this->minute_late = 0;
            $this->point_late  = 0;
        }

        

        return $this;
    }

    

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNote(): string
    {
        return $this->status_note;
    }

    public function getPointPayroll(): int
    {
        return $this->gaji;
    }

    public function getMinuteLate(): int
    {
        return $this->minute_late;   
    }

    public function getPointLate(): int
    {
        return $this->point_late;   
    }
}

class OvertimeData extends AbsenceData
{

    private $point_overtime       = 0;
    private $date_overtime        = '0000-00-00';
    private $datetime_endOvertime = NULL;
    private $note                 = '';
    private $check_leader         = 0;

    private $schedule   = NULL;
    private $attendance = NULL;

    public function __construct()
    {
        $this->schedule   = new ScheduleData;
        $this->attendance = new AttendanceData;
    }

    public function process()
    {
        $jobOvertime = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->where('employee.id', $this->id_employee)
            ->select('book_overtime', 'min_overtime')
            ->first();

        $overtime = Overtime::where('id_employee', $this->id_employee)
            ->where('date', $this->date)
            ->first();


        if ($overtime) {
            $this->date_overtime        = $overtime->date;
            $this->datetime_endOvertime = $overtime->end_overtime;
            $this->note                 = $overtime->note;
            $this->check_leader         = $overtime->check_leader;
        }
        else
        {
            $this->date_overtime        = '0000-00-00';
            $this->datetime_endOvertime = NULL;
            $this->note                 = '';
            $this->check_leader         = 0;
        }

        $schedule   = $this->schedule->setDate($this->date)->setIdEmployee($this->id_employee)->process();
        $attendance = $this->attendance->setDate($this->date)->setIdEmployee($this->id_employee)->setCheck($this->check_in, $this->check_out)->process();

        if ($jobOvertime->book_overtime)
        {
            if ($overtime)
            {
                $lowOvertime = min(strtotime($this->date . ' ' . $this->check_out), strtotime($this->datetime_endOvertime));

                $totalOvertime = $lowOvertime - strtotime($this->date . ' ' . $schedule->getTimeShiftOut());

                $clockOvertime = (int) (($totalOvertime / 60) / 15) / 4;
                if ($jobOvertime->min_overtime < (int) ($totalOvertime / 60) && $clockOvertime > 4)
                {

                    $this->point_overtime = 4 * $attendance->getPointPayroll() + (($clockOvertime - 4) * (1.5 + $attendance->getPointPayroll()));
                }
                else
                {
                    $this->point_overtime = $clockOvertime;
                }
            }

        }
        else
        {
            if (strtotime($schedule->getTimeShiftOut()) < strtotime($this->check_out))
            {
                $totalOvertime = strtotime($this->check_out) - strtotime($schedule->getTimeShiftOut());

                if ($attendance->getStatus() == 'kosong')
                {
                    $totalOvertime = strtotime($this->check_out) - strtotime($this->check_in);
                }

                $clockOvertime = (int) (($totalOvertime / 60) / 15) / 4;

                if ($jobOvertime->min_overtime < (int) ($totalOvertime / 60) && $clockOvertime > 4)
                {
                    $this->point_overtime = 4 * $attendance->getPointPayroll() + (($clockOvertime - 4) * (1.5 + $attendance->getPointPayroll()));
                }
                else
                {
                    $this->point_overtime = $clockOvertime;
                }
            }
        }

        return $this;
    }

    public function getPointOvertime(): string
    {
        return $this->point_overtime;   
    }

    public function getDateRequest(): string
    {
        return $this->date_overtime;   
    }

    public function getDatetimeOvertime()
    {
        return $this->datetime_endOvertime;   
    }

    public function getNote(): string
    {
        return $this->note;   
    }

    public function isCheckLeader(): bool
    {
        return $this->check_leader == 1 ? true : false;
    }
}
    
class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function sumOvertime(OvertimePoint $overtimePoint)
    {
        $sumOvertime = 0;

        if ($overtimePoint->book_overtime) {
            if ($overtimePoint->time_overtime) {
                $lowOvertime = min(strtotime($overtimePoint->time_out), strtotime($overtimePoint->time_overtime));

                $totalOvertime = $lowOvertime - strtotime($overtimePoint->schedule_out);
                $clockOvertime = (int) (($totalOvertime / 60) / 15) / 4;
                if ($overtimePoint->min_overtime < (int) ($totalOvertime / 60)) {
                    if ($clockOvertime > 4) {
                        $sumOvertime = 4 + (($clockOvertime - 4) * 1.5);
                    } else {
                        $sumOvertime = $clockOvertime;
                    }
                }
            }
        } else {
            if (strtotime($overtimePoint->schedule_out) < strtotime($overtimePoint->time_out)) {
                $totalOvertime = strtotime($overtimePoint->time_out) - strtotime($overtimePoint->schedule_out);
                if ($overtimePoint->status == 'kosong') {
                    $totalOvertime = strtotime($overtimePoint->time_out) - strtotime($overtimePoint->time_in);
                }
                $clockOvertime = (int) (($totalOvertime / 60) / 15) / 4;

                if ($overtimePoint->min_overtime < $clockOvertime) {
                    if ($clockOvertime > 4) {
                        $sumOvertime = 4 * $overtimePoint->gaji + (($clockOvertime - 4) * (1.5 + $overtimePoint->gaji));
                    } else {
                        $sumOvertime = $clockOvertime;
                    }
                }
            }
        }

        return $sumOvertime;
    }

    public function index(Request $request)
    {
        $index = Absence::all();

        return view('backend.absence.index')->with(compact('index'));
    }

    public function employee($id, Request $request)
    {
        $index   = AbsenceEmployee::where('id_absence', $id)->get();
        $absence = Absence::find($id);

        return view('backend.absence.employee')->with(compact('index', 'absence'));
    }

    public function employeeDetail($id, Request $request)
    {
        $index = AbsenceEmployeeDetail::where('id_absence_employee', $id)->get();

        $absenceEmployee = AbsenceEmployee::find($id);

        $absenceEmployeeDetail = AbsenceEmployeeDetail::where('id_absence_employee', $id)->get();

        $masuk   = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'masuk')->count();
        $libur   = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'libur')->count();
        $sakit   = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'sakit')->count();
        $izin    = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'izin')->count();
        $cuti    = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'cuti')->count();
        $alpa    = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'alpa')->count();
        $present = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', '<>', 'kosong')->count();
        $gaji    = AbsenceEmployeeDetail::where('id_absence_employee', $id)->sum('gaji');
        $lembur  = AbsenceEmployeeDetail::where('id_absence_employee', $id)->sum('point_overtime');
        $telat   = AbsenceEmployeeDetail::where('id_absence_employee', $id)->sum('total_late');

        return view('backend.absence.employeeDetail')->with(compact(
            'index',
            'absenceEmployee',
            'absenceEmployeeDetail',
            'masuk',
            'libur',
            'sakit',
            'izin',
            'cuti',
            'alpa',
            'present',
            'gaji',
            'lembur',
            'telat',
            'id'
        ));
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
        $end_periode   = $periode[1];

        // get data from excel
        $data = '';
        if ($request->hasFile('excel')) {
            $data = Excel::load($request->file('excel')->getRealPath(), function ($reader) {})->get();
            // return $data;
        }

        DB::beginTransaction();

        if (!empty($data)) {
            $absence = new Absence;

            $absence->name       = $request->name;
            $absence->date_start = date('Y-m-d', strtotime($start_periode));
            $absence->date_end   = date('Y-m-d', strtotime($end_periode));

            $absence->save();

            // grouping by sorted no._id and insert into AbsenceEmployee
            $return       = '';
            $init_machine = 0;
            foreach ($data as $list) {

                if ($init_machine != $list['no._id']) {
                    $init_machine = $list['no._id'];
                    $insert[]     = ['id_absence' => $absence->id, 'id_machine' => (int) $init_machine];
                }
            }

            // insert into AbsenceEmployeeDetail
            if (!empty($insert)) {
                AbsenceEmployee::insert($insert);

                $absenceEmployee = AbsenceEmployee::where('id_absence', $absence->id)->get();
                $overtimePoint = new OvertimePoint;

                $holiday    = new HolidayData;
                $schedule   = new ScheduleData;
                $overtime   = new OvertimeData;
                $attendance = new AttendanceData;
                $dayoff     = new DayoffData;

                foreach ($absenceEmployee as $list) {

                    if (!empty($list->employee)) {

                        $start = $list->absence->date_start;

                        while ($start <= $list->absence->date_end) {
                            $date[] = $start;
                            $start  = date('Y-m-d', strtotime($start . ' +1 day'));
                        }

                        foreach ($data as $list2) {
                            $date_explode = explode('/', $list2['tanggal']);

                            $format_date  = $date_explode[0];
                            $format_month = $date_explode[1];
                            $format_year  = $date_explode[2];

                            $format_date_php = $format_year . '-' . $format_month . '-' . $format_date;

                            $check_in  = $list2['scan_masuk'];
                            $check_out = $list2['scan_pulang'];

                            $schedule = $schedule
                                ->setDate($format_date_php)
                                ->setIdEmployee($list->employee->id)
                                ->setCheck($check_in, $check_out)
                                ->process();

                            $overtime = $overtime
                                ->setDate($format_date_php)
                                ->setIdEmployee($list->employee->id)
                                ->setCheck($check_in, $check_out)
                                ->process();

                            $attendance = $attendance
                                ->setDate($format_date_php)
                                ->setIdEmployee($list->employee->id)
                                ->setCheck($check_in, $check_out)
                                ->process();

                            if ($list->id_machine == $list2['no._id']) {
                                $insert2[] = [
                                    'id_absence_employee' => $list->id,
                                    'schedule_in'         => $schedule->isSchedule() ? $format_date_php . ' ' . $schedule->getTimeShiftIn() : '',
                                    'schedule_out'        => $schedule->isSchedule() ? $format_date_php . ' ' . $schedule->getTimeShiftOut() : '',
                                    'time_in'             => $check_in ? $format_date_php . ' ' . $check_in : '',
                                    'time_out'            => $check_out ? $format_date_php . ' ' . $check_out : '',
                                    'status'              => $attendance->getStatus(),
                                    'status_note'         => $attendance->getNote(),
                                    'gaji'                => $attendance->getPointPayroll(),
                                    'gaji_pokok'          => $list->employee->gaji_pokok,
                                    'time_overtime'       => $overtime->getDatetimeOvertime(),
                                    'point_overtime'      => $overtime->getPointOvertime(),
                                    'payment_overtime'    => $list->employee->uang_lembur,
                                    'total_late'          => $attendance->getPointLate(),
                                    'fine_late'           => $list->employee->uang_telat,
                                ];
                            }
                        }
                    }
                }

                DB::rollBack();
                return $insert2;

                if (!empty($insert2)) {
                    AbsenceEmployeeDetail::insert($insert2);
                }
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
        $end_periode   = $periode[1];

        $index = Absence::find($id);

        $index->name       = $request->name;
        $index->date_start = date('Y-m-d', strtotime($start_periode));
        $index->date_end   = date('Y-m-d', strtotime($end_periode));

        $index->save();

        Session::flash('success', 'Data Has Been Updated');
        return redirect()->route('admin.absence');
    }

    public function delete($id, Request $request)
    {
        if (isset($request->type) && $request->type == 'absenceEmployee') {
            AbsenceEmployee::destroy($id);
        } else if (isset($request->type) && $request->type == 'absenceEmployeeDetail') {
            AbsenceEmployeeDetail::destroy($id);
        } else {
            Absence::destroy($id);
        }

        Session::flash('success', 'Data Has Been Deleted');
        return redirect()->route('admin.absence');
    }

    public function action(Request $request)
    {
        if (isset($request->id)) {
            if ($request->action == 'delete') {
                if (isset($request->type) && $request->type == 'absenceEmployee') {
                    AbsenceEmployee::destroy($request->id);
                } else if (isset($request->type) && $request->type == 'absenceEmployeeDetail') {
                    AbsenceEmployeeDetail::destroy($request->id);
                } else {
                    Absence::destroy($request->id);
                }

                Session::flash('success', 'Data Selected Has Been Deleted');
            } else if ($request->action == 'enable') {
                $index = Absence::whereIn('id', $request->id)->update(['active' => 1]);
                Session::flash('success', 'Data Selected Has Been Actived');
            } else if ($request->action == 'disable') {
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

        if ($action == 1) {
            Session::flash('success', 'Data Has Been Actived');
        } else {
            Session::flash('success', 'Data Has Been Inactived');
        }

        return redirect()->route('admin.absence');
    }

    public function getDataAbsenceEmployeeDetail(Request $request)
    {
        $absenceEmployee = AbsenceEmployee::find($request->id_absence_employee);

        $holiday = Holiday::where('date', date('Y-m-d', strtotime($request->date)))->first();

        $dayoff = Dayoff::where('start_dayoff', date('Y-m-d', strtotime($request->date)))->where('id_employee', $absenceEmployee->employee->id)->first();

        $shift = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
            ->join('shift_detail', 'shift_detail.id_shift', '=', 'attendance.id_shift')
            ->where('employee.id', $absenceEmployee->employee->id)
            ->where('day', date('w', strtotime($request->date)))
            ->select('day', 'shift_in', 'shift_out')
            ->first();

        $overtime = Overtime::where('id_employee', $absenceEmployee->employee->id)->whereDate('date', date('Y-m-d', strtotime($request->date)))->first();

        $status = $status_note = null;
        
        if ($holiday) {
            $status      = 'libur';
            $status_note = $holiday->name;
        } else if ($dayoff && $dayoff->start_dayoff <= date('Y-m-d', strtotime($request->date)) && date('Y-m-d', strtotime($request->date)) <= $dayoff->end_dayoff) {
            $status      = 'cuti';
            $status_note = $dayoff->note;
        }
        
        return compact('status', 'status_note', 'shift', 'overtime');
    }

    public function createAbsenceEmployeeDetail($id)
    {
        $absenceEmployee = AbsenceEmployee::find($id);
        return view('backend.absence.createEmployeeDetail')->with(compact('absenceEmployee'));
    }

    public function storeAbsenceEmployeeDetail($id, Request $request)
    {
        $absenceEmployee = AbsenceEmployee::find($id);

        $gaji = 0;
        
        if ($request->status == 'masuk' && $request->time_in && $request->time_out) {
            $gaji = 1;
        }
        if ($request->status == 'kosong' && $request->time_in && $request->time_out) {
            $gaji = 1.5;
        }
        if ($request->status == 'libur' && $request->time_in && $request->time_out){
            $gaji = 1.5;
        }
        if(in_array($request->status, ['libur', 'izin', 'cuti', 'sakit']) && !$request->time_in && !$request->time_out)
        {
            $gaji = 1;
        }
        if($request->status == 'alpa')
        {
            $gaji = -1;
            $request->time_in = '00:00:00';
            $request->time_out = '00:00:00';
        }

        $schedule_in  = date('Y-m-d', strtotime($request->date)).' '.date('H:i:s', strtotime($request->schedule_in));
        $schedule_out = date('Y-m-d', strtotime($request->date)).' '.date('H:i:s', strtotime($request->schedule_out));
        $time_in      = date('Y-m-d', strtotime($request->date)).' '.date('H:i:s', strtotime($request->time_in));
        $time_out     = date('Y-m-d', strtotime($request->date)).' '.date('H:i:s', strtotime($request->time_out));

        $overtime = new OvertimeData;

        $overtime = $overtime->setDate($request->date)->setIdEmployee($absenceEmployee->employee->id)->setCheck($request->time_in, $request->time_out)->process();

        $totalLate = $fine_late = 0;
        if ($request->status != 'kosong' && $schedule_in < $time_in) {
            $totalLate = strtotime($time_in) - strtotime($schedule_in);
            $totalLate = (int) (($totalLate / 60) / $shift->late);
        }

        $this->validate($request, [
            'schedule_in' => 'required',
            'schedule_out' => 'required',
            'time_in' => 'required_unless:status,kosong',
            'time_out' => 'required_unless:status,kosong',
            'status_note' => 'required_unless:status,kosong,status,masuk',
            'gaji_pokok' => 'required|numeric',
            'time_overtime' => 'nullable|date',
            'fine_late' => 'required',
        ]);


        $index = new AbsenceEmployeeDetail;
    
        $index->id_absence_employee = $id;
        $index->schedule_in         = $schedule_in;
        $index->schedule_out        = $schedule_out;
        $index->time_in             = $time_in;
        $index->time_out            = $time_out;
        $index->status              = $request->status;
        $index->status_note         = $request->status_note;
        $index->gaji                = $request->gaji ?: $gaji;
        $index->gaji_pokok          = $request->gaji_pokok;
        $index->time_overtime       = date('Y-m-d H:i:s', strtotime($request->time_overtime));
        $index->point_overtime      = $request->point_overtime ?: $overtime->getPointOvertime();
        $index->payment_overtime    = $request->payment_overtime;
        $index->total_late          = $totalLate;
        $index->fine_late           = $request->fine_late;
        $index->fine_additional     = $request->fine_additional;

        $index->save();

        return redirect()->route('admin.absence.employeeDetail', ['id' => $id])->with('success', 'Data has been added.');
    }

    public function editAbsenceEmployeeDetail($id)
    {
        $index           = AbsenceEmployeeDetail::find($id);
        $absenceEmployee = AbsenceEmployee::find($index->id_absence_employee);
        return view('backend.absence.editEmployeeDetail')->with(compact('index', 'absenceEmployee'));
    }

    public function updateAbsenceEmployeeDetail($id, Request $request)
    {
        $absenceEmployee = AbsenceEmployee::find($id);

        $gaji = 0;
        
        if ($request->status == 'masuk' && $request->time_in && $request->time_out) {
            $gaji = 1;
        }
        if ($request->status == 'kosong' && $request->time_in && $request->time_out) {
            $gaji = 1.5;
        }
        if ($request->status == 'libur' && $request->time_in && $request->time_out){
            $gaji = 1.5;
        }
        if(in_array($request->status, ['libur', 'izin', 'cuti', 'sakit']) && !$request->time_in && !$request->time_out)
        {
            $gaji = 1;
        }
        if($request->status == 'alpa')
        {
            $gaji = -1;
            $request->time_in = '00:00:00';
            $request->time_out = '00:00:00';
        }

        $schedule_in  = date('Y-m-d', strtotime($request->date)).' '.date('H:i:s', strtotime($request->schedule_in));
        $schedule_out = date('Y-m-d', strtotime($request->date)).' '.date('H:i:s', strtotime($request->schedule_out));
        $time_in      = date('Y-m-d', strtotime($request->date)).' '.date('H:i:s', strtotime($request->time_in));
        $time_out     = date('Y-m-d', strtotime($request->date)).' '.date('H:i:s', strtotime($request->time_out));

        $overtime = new OvertimeData;

        $overtime = $overtime->setDate($request->date)->setIdEmployee($absenceEmployee->employee->id)->setCheck($request->time_in, $request->time_out)->process();

        $totalLate = $fine_late = 0;
        if ($request->status != 'kosong' && $schedule_in < $time_in) {
            $totalLate = strtotime($time_in) - strtotime($schedule_in);
            $totalLate = (int) (($totalLate / 60) / $shift->late);
        }

        $this->validate($request, [
            'schedule_in' => 'required',
            'schedule_out' => 'required',
            'time_in' => 'required_unless:status,kosong',
            'time_out' => 'required_unless:status,kosong',
            'status_note' => 'required_unless:status,kosong,status,masuk',
            'gaji_pokok' => 'required|numeric',
            'time_overtime' => 'nullable|date',
            'fine_late' => 'required',
        ]);


        $index = AbsenceEmployeeDetail::find($request->id);
    
        $index->schedule_in         = $schedule_in;
        $index->schedule_out        = $schedule_out;
        $index->time_in             = $time_in;
        $index->time_out            = $time_out;
        $index->status              = $request->status;
        $index->status_note         = $request->status_note;
        $index->gaji                = $request->gaji ?: $gaji;
        $index->gaji_pokok          = $request->gaji_pokok;
        $index->time_overtime       = date('Y-m-d H:i:s', strtotime($request->time_overtime));
        $index->point_overtime      = $request->point_overtime ?: $overtime->getPointOvertime();
        $index->payment_overtime    = $request->payment_overtime;
        $index->total_late          = $totalLate;
        $index->fine_late           = $request->fine_late;
        $index->fine_additional     = $request->fine_additional;

        $index->save();

        return redirect()->route('admin.absence.employeeDetail', ['id' => $id])->with('success', 'Data has been added.');
    }


}
