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
use Illuminate\Support\Facades\DB;
use Session;

class Grab
{
    public $holiday;
    public $shift;
    public $shift_detail;
    public $dayoff;
    public $overtime;
    public $job_overtime;
}

class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function holidayData(Grab $grab, string $date = '0000-00-00')
    {
        $name       = '';
        $type       = '';
        $is_holiday = false;

        foreach ($grab->holiday as $list) {
            if ($date == $list->date) {
                $name       = $list->name;
                $type       = $list->type;
                $is_holiday = true;
                break;
            }
        }

        return compact('name', 'type', 'is_holiday');
    }

    public function scheduleData(Grab $grab, string $date = '0000-00-00')
    {
        $day         = null;
        $shift_in    = null;
        $shift_out   = null;
        $is_schedule = false;

        foreach ($grab->shift_detail as $list) {
            if (date('w', strtotime($date)) == $list->day) {
                $day         = $list->day;
                $shift_in    = $list->shift_in;
                $shift_out   = $list->shift_out;
                $is_schedule = true;
                break;
            }
        }

        return compact('day', 'shift_in', 'shift_out', 'is_schedule');
    }

    public function dayoffData(Grab $grab, string $date = '0000-00-00')
    {
        $start_dayoff = '0000-00-00';
        $end_dayoff   = '0000-00-00';
        $total_dayoff = 0;
        $note         = '';
        $type         = '';
        $is_dayoff    = false;

        foreach ($grab->dayoff as $list) {
            if ($list->start_dayoff <= $date && $date <= $list->end_dayoff) {
                $start_dayoff = $list->start_dayoff;
                $end_dayoff   = $list->end_dayoff;
                $total_dayoff = $list->total_dayoff;
                $note         = $list->note;
                $type         = $list->type;
                $is_dayoff    = true;
                break;
            }
        }

        return compact('start_dayoff', 'end_dayoff', 'total_dayoff', 'note', 'type', 'is_dayoff');
    }

    public function attendanceData(Grab $grab, string $date = '0000-00-00', string $check_in = null, string $check_out = null)
    {

        $late = 0;
        if ($grab->shift) {
            $late = $grab->shift->late;
        }

        $holiday  = $this->holidayData($grab, $date);
        $schedule = $this->scheduleData($grab, $date);
        $dayoff   = $this->dayoffData($grab, $date);

        $status_note   = '';
        $point_payroll = 0;

        if ($schedule['is_schedule'] && $check_in && $check_out) {
            $status        = 'masuk';
            $point_payroll = 1;

            if ($holiday['is_holiday']) {
                $status        = 'libur';
                $status_note   = $holiday['name'];
                $point_payroll = 1.5;
            }

        } else if ($schedule['is_schedule'] === false && $check_in && $check_out) {
            $status        = 'masuk';
            $point_payroll = 1.5;
        } else if ($holiday['is_holiday']) {
            $status      = 'libur';
            $status_note = $holiday['name'];

            if ($schedule['is_schedule']) {
                $point_payroll = 1;
            }
        } else if ($dayoff['is_dayoff']) {
            $status        = $dayoff['type'];
            $status_note   = $dayoff['note'];
            $point_payroll = 1;
        } else if ($schedule['is_schedule'] === false) {
            $status        = 'kosong';
            $point_payroll = 0;
        } else {
            $status        = 'alpa';
            $point_payroll = -1;
        }

        if ($schedule['is_schedule'] && strtotime($schedule['shift_in']) < strtotime($check_in)) {
            $point_late  = strtotime($check_in) - strtotime($schedule['shift_in']);
            $minute_late = (int) (($point_late / 60));
            $point_late  = (int) (($minute_late - 1) / max($late, 1)) + 1;
        } else {
            $minute_late = 0;
            $point_late  = 0;
        }

        return compact('status', 'status_note', 'point_payroll', 'minute_late', 'point_late');
    }

    public function overtimeData(Grab $grab, string $date = '0000-00-00', string $check_in = null, string $check_out = null)
    {
        $date_overtime        = '0000-00-00';
        $datetime_endOvertime = null;
        $note                 = '';
        $check_leader         = 0;

        foreach ($grab->overtime as $list) {
            if ($date == $list->date) {
                $date_overtime        = $list->date;
                $datetime_endOvertime = $list->end_overtime;
                $note                 = $list->note;
                $check_leader         = $list->check_leader;
                break;
            }
        }

        $schedule   = $this->scheduleData($grab, $date);
        $attendance = $this->attendanceData($grab, $date, $check_in, $check_out);

        $point_overtime = 0;
        if ($grab->job_overtime->book_overtime) {
            if ($datetime_endOvertime) {
                $lowOvertime = min(strtotime($date . ' ' . $check_out), strtotime($datetime_endOvertime));

                $totalOvertime = $lowOvertime - strtotime($date . ' ' . $schedule['shift_out']);

                $clockOvertime = (int) (($totalOvertime / 60) / 15) / 4;
                if ($grab->job_overtime->min_overtime < (int) ($totalOvertime / 60) && $clockOvertime > 4) {

                    $point_overtime = 4 * $attendance['point_payroll'] + (($clockOvertime - 4) * (1.5 + $attendance['point_payroll']));
                } else {
                    $point_overtime = $clockOvertime;
                }
            }

        } else {
            if (strtotime($schedule['shift_out']) < strtotime($check_out)) {
                $totalOvertime = strtotime($check_out) - strtotime($schedule['shift_out']);

                if ($attendance['status'] === 'kosong') {
                    $totalOvertime = strtotime($check_out) - strtotime($check_in);
                }

                $clockOvertime = (int) (($totalOvertime / 60) / 15) / 4;

                if ($grab->job_overtime->min_overtime < (int) ($totalOvertime / 60) && $clockOvertime > 4) {
                    $point_overtime = 4 * $attendance['point_payroll'] + (($clockOvertime - 4) * (1.5 + $attendance['point_payroll']));
                } else {
                    $point_overtime = $clockOvertime;
                }
            }
        }

        return compact('date_overtime', 'datetime_endOvertime', 'note', 'check_leader', 'point_overtime');
    }

    public function combineDatetime($date, $time = '00:00:00')
    {
        return date('Y-m-d', strtotime($date)) . ' ' . date('H:i:s', strtotime($time));
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

        $absenceEmployeeDetail = AbsenceEmployeeDetail::where('id_absence_employee', $id)->orderBy('date', 'ASC')->get();

        $masuk   = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'masuk')->count();
        $libur   = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'libur')->count();
        $sakit   = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'sakit')->count();
        $izin    = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'izin')->count();
        $cuti    = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'cuti')->count();
        $alpa    = AbsenceEmployeeDetail::where('id_absence_employee', $id)->where('status', 'alpa')->count();
        $present = $absenceEmployee->per_day;
        $gaji    = AbsenceEmployeeDetail::where('id_absence_employee', $id)->sum('gaji');
        $lembur  = AbsenceEmployeeDetail::where('id_absence_employee', $id)->sum('point_overtime');
        $telat   = AbsenceEmployeeDetail::where('id_absence_employee', $id)->sum('point_late');

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
        // ALTER TABLE `absence_employee_detail` ADD `date` DATE NOT NULL AFTER `id_absence_employee`;
        // UPDATE `absence_employee_detail` SET `date` =`schedule_in`;
        // ALTER TABLE `absence_employee_detail` CHANGE `time_in` `check_in` TIME NULL DEFAULT NULL, CHANGE `time_out` `check_out` TIME NULL DEFAULT NULL, CHANGE `schedule_in` `schedule_in` TIME NULL DEFAULT NULL, CHANGE `schedule_out` `schedule_out` TIME NULL DEFAULT NULL;

        $this->validate($request, [
            'name' => 'required',
            'date' => 'required',
        ]);

        $periode = explode(' - ', $request->date);

        $start_periode = $periode[0];
        $end_periode   = $periode[1];

        // get data from excel
        $data = '';
        if ($request->hasFile('excel'))
        {
            $data = Excel::load($request->file('excel')->getRealPath(), function ($reader) {})->get();
        }

        // DB::beginTransaction();

        if ( !empty($data) )
        {
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

                    $per_day = Employee::where('id_machine', $init_machine)->first();

                    if($per_day)
                    {
                        $per_day = $per_day->jobTitle->per_day;
                    }
                    else
                    {
                        $per_day = 1;
                    }

                    $insert[]     = ['id_absence' => $absence->id, 'id_machine' => (int) $init_machine, 'per_day' => $per_day];
                }
            }

            // insert into AbsenceEmployeeDetail
            if ( !empty($insert) )
            {
                AbsenceEmployee::insert($insert);

                $absenceEmployee = AbsenceEmployee::where('id_absence', $absence->id)->get();

                foreach ($absenceEmployee as $list)
                {
                    if (!empty($list->employee))
                    {
                        $start = $list->absence->date_start;

                        while ($start <= $list->absence->date_end)
                        {
                            $date[] = $start;
                            $start  = date('Y-m-d', strtotime($start . ' +1 day'));
                        }

                        $grab = new Grab;

                        $grab->holiday = Holiday::whereIn('date', $date)->get();

                        $grab->shift = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
                            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
                            ->join('shift', 'shift.id', '=', 'attendance.id_shift')
                            ->where('employee.id', $list->employee->id)
                            ->select('late')
                            ->first();

                        $grab->shift_detail = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
                            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
                            ->join('shift_detail', 'shift_detail.id_shift', '=', 'attendance.id_shift')
                            ->where('employee.id', $list->employee->id)
                            ->select('day', 'shift_in', 'shift_out')
                            ->get();

                        $grab->dayoff = Dayoff::whereIn('start_dayoff', $date)->where('id_employee', $list->employee->id)->get();

                        $grab->overtime = Overtime::where('id_employee', $list->employee->id)->get();

                        $grab->job_overtime = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
                            ->where('employee.id', $list->employee->id)
                            ->select('book_overtime', 'min_overtime')
                            ->first();

                        $inserted_date = '';

                        foreach ($data as $list2)
                        {
                            $date_explode = explode('/', $list2['tanggal']);

                            $format_date  = $date_explode[0];
                            $format_month = $date_explode[1];
                            $format_year  = $date_explode[2];

                            $format_date_php = $format_year . '-' . $format_month . '-' . $format_date;

                            $check_in  = $list2['scan_masuk'];
                            $check_out = $list2['scan_pulang'];

                            $schedule   = $this->scheduleData($grab, $format_date_php);
                            $overtime   = $this->overtimeData($grab, $format_date_php, $check_in, $check_out);
                            $attendance = $this->attendanceData($grab, $format_date_php, $check_in, $check_out);

                            $inserted_date[] = $format_date_php;

                            if ($list->id_machine == $list2['no._id']) {
                                $insert2[] = [
                                    'id_absence_employee' => $list->id,
                                    'date'                => $format_date_php,
                                    'schedule_in'         => $schedule['is_schedule'] ? $schedule['shift_in'] : null,
                                    'schedule_out'        => $schedule['is_schedule'] ? $schedule['shift_out'] : null,
                                    'check_in'            => $check_in ?? null,
                                    'check_out'           => $check_out ?? null,
                                    'status'              => $attendance['status'],
                                    'status_note'         => $attendance['status_note'],
                                    'gaji'                => $attendance['point_payroll'],
                                    'gaji_pokok'          => $list->employee->gaji_pokok,
                                    'time_overtime'       => $overtime['datetime_endOvertime'],
                                    'point_overtime'      => $overtime['point_overtime'],
                                    'payment_overtime'    => $list->employee->uang_lembur,
                                    'point_late'          => $attendance['point_late'],
                                    'fine_late'           => $list->employee->uang_telat,
                                ];
                            }
                        }

                        foreach ($date as $list2) {
                            if(!in_array($list2, $inserted_date))
                            {
                                $attendance = $this->attendanceData($grab, $list2);
                                
                                $insert2[] = [
                                    'id_absence_employee' => $list->id,
                                    'date'                => $list2,
                                    'schedule_in'         => null,
                                    'schedule_out'        => null,
                                    'check_in'            => null,
                                    'check_out'           => null,
                                    'status'              => $attendance['status'],
                                    'status_note'         => $attendance['status_note'],
                                    'gaji'                => $attendance['point_payroll'],
                                    'gaji_pokok'          => $list->employee->gaji_pokok,
                                    'time_overtime'       => null,
                                    'point_overtime'      => 0,
                                    'payment_overtime'    => $list->employee->uang_lembur,
                                    'point_late'          => $attendance['point_late'],
                                    'fine_late'           => $list->employee->uang_telat,
                                ];
                            }
                        }
                    }
                } // endforeach

                // DB::rollBack();
                // return $insert2;

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

        $grab = new Grab;

        $grab->holiday = Holiday::where('date', $request->date)->get();

        $grab->shift = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
            ->join('shift', 'shift.id', '=', 'attendance.id_shift')
            ->where('employee.id', $absenceEmployee->employee->id)
            ->select('late')
            ->first();

        $grab->shift_detail = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
            ->join('shift_detail', 'shift_detail.id_shift', '=', 'attendance.id_shift')
            ->where('employee.id', $absenceEmployee->employee->id)
            ->select('day', 'shift_in', 'shift_out')
            ->get();

        $grab->dayoff = Dayoff::where('start_dayoff', $request->date)->where('id_employee', $absenceEmployee->employee->id)->get();

        $grab->overtime = Overtime::where('id_employee', $absenceEmployee->employee->id)->get();

        $grab->job_overtime = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->where('employee.id', $absenceEmployee->employee->id)
            ->select('book_overtime', 'min_overtime')
            ->first();

        $overtime   = $this->overtimeData($grab, $request->date, $request->check_in, $request->check_out);
        $attendance = $this->attendanceData($grab, $request->date, $request->check_in, $request->check_out);

        $this->validate($request, [
            'date'          => 'required|date',
            'schedule_in'   => 'required',
            'schedule_out'  => 'required',
            'check_in'      => 'required_unless:status,kosong',
            'check_out'     => 'required_unless:status,kosong',
            'status_note'   => 'required_unless:status,kosong,status,masuk',
            'gaji_pokok'    => 'required|numeric',
            'time_overtime' => 'nullable|date',
            'fine_late'     => 'required',
        ]);

        $index = new AbsenceEmployeeDetail;

        $index->id_absence_employee = $id;
        $index->date                = date('Y-m-d', strtotime($request->date));
        $index->schedule_in         = date('H:i:s', strtotime($request->schedule_in));
        $index->schedule_out        = date('H:i:s', strtotime($request->schedule_out));
        $index->check_in            = date('H:i:s', strtotime($request->check_in));
        $index->check_out           = date('H:i:s', strtotime($request->check_out));
        $index->status              = $request->status;
        $index->status_note         = $request->status_note;
        $index->gaji                = $request->gaji ?: $attendance['point_payroll'];
        $index->gaji_pokok          = $request->gaji_pokok;
        $index->time_overtime       = $request->time_overtime ? date('Y-m-d H:i:s', strtotime($request->time_overtime)) : null;
        $index->point_overtime      = $request->point_overtime ?: $overtime['point_overtime'];
        $index->payment_overtime    = $request->payment_overtime;
        $index->point_late          = $attendance['point_late'];
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

        $grab = new Grab;

        $grab->holiday = Holiday::where('date', $request->date)->get();

        $grab->shift = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
            ->join('shift', 'shift.id', '=', 'attendance.id_shift')
            ->where('employee.id', $absenceEmployee->employee->id)
            ->select('late')
            ->first();

        $grab->shift_detail = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->join('attendance', 'attendance.id_job_title', '=', 'job_title.id')
            ->join('shift_detail', 'shift_detail.id_shift', '=', 'attendance.id_shift')
            ->where('employee.id', $absenceEmployee->employee->id)
            ->select('day', 'shift_in', 'shift_out')
            ->get();

        $grab->dayoff = Dayoff::where('start_dayoff', $request->date)->where('id_employee', $absenceEmployee->employee->id)->get();

        $grab->overtime = Overtime::where('id_employee', $absenceEmployee->employee->id)->get();

        $grab->job_overtime = Employee::join('job_title', 'job_title.id', '=', 'employee.id_job_title')
            ->where('employee.id', $absenceEmployee->employee->id)
            ->select('book_overtime', 'min_overtime')
            ->first();

        $overtime   = $this->overtimeData($grab, $request->date, $request->check_in, $request->check_out);
        $attendance = $this->attendanceData($grab, $request->date, $request->check_in, $request->check_out);

        $this->validate($request, [
            'date'          => 'required|date',
            'schedule_in'   => 'required',
            'schedule_out'  => 'required',
            'check_in'      => 'required_unless:status,kosong',
            'check_out'     => 'required_unless:status,kosong',
            'status_note'   => 'required_unless:status,kosong,status,masuk',
            'gaji_pokok'    => 'required|numeric',
            'time_overtime' => 'nullable|date',
            'fine_late'     => 'required',
        ]);

        $index = AbsenceEmployeeDetail::find($request->id);

        $index->date             = date('Y-m-d', strtotime($request->date));
        $index->schedule_in      = date('H:i:s', strtotime($request->schedule_in));
        $index->schedule_out     = date('H:i:s', strtotime($request->schedule_out));
        $index->check_in         = date('H:i:s', strtotime($request->check_in));
        $index->check_out        = date('H:i:s', strtotime($request->check_out));
        $index->status           = $request->status;
        $index->status_note      = $request->status_note;
        $index->gaji             = $request->gaji ?: $attendance['point_payroll'];
        $index->gaji_pokok       = $request->gaji_pokok;
        $index->time_overtime    = $request->time_overtime ? date('Y-m-d H:i:s', strtotime($request->time_overtime)) : null;
        $index->point_overtime   = $request->point_overtime ?: $overtime['point_overtime'];
        $index->payment_overtime = $request->payment_overtime;
        $index->point_late       = $attendance['point_late'];
        $index->fine_late        = $request->fine_late;
        $index->fine_additional  = $request->fine_additional;

        $index->save();

        return redirect()->route('admin.absence.employeeDetail', ['id' => $id])->with('success', 'Data has been added.');
    }

    public function deleteAbsenceEmployeeDetail($id, Request $request)
    {
        AbsenceEmployeeDetail::destroy($request->id);

        return redirect()->back()->with('success', 'Data has been deleted.');
    }

    public function changePerday($id, Request $request)
    {
        $this->validate($request, [
            'per_day'   => 'required|integer|min:1',
        ]);

        $index = AbsenceEmployee::find($id);

        $index->per_day = $request->per_day;

        $index->save();

        return redirect()->back()->with('success', 'Data Has Been Updated');
    }
}
