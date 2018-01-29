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
use Illuminate\Support\Facades\Auth;
use Session;
use Datatables;

class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function combineDatetime($date, $time = '00:00:00')
    {
        return date('Y-m-d', strtotime($date)) . ' ' . date('H:i:s', strtotime($time));
    }

    public function index(Request $request)
    {
        return view('backend.absence.index')->with(compact('request'));
    }

    public function datatables(Request $request)
    {
        $index = Absence::all();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('view-absence'))
            {
                $html .= '
                    <a href="' . route('admin.absence.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-eye"></i></a>
                ';
            }

            if(Auth::user()->can('delete-absence'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-absence" data-toggle="modal" data-target="#delete-absence" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            return $html;
        });

        $datatables->editColumn('date_start', function ($index) {
            return date('d/m/Y', strtotime($index->date_start)) . '-' . date('d/m/Y', strtotime($index->date_end));
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

    public function create(Request $request)
    {
        return view('backend.absence.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'date' => 'required',
        ]);

        $periode = explode(' - ', $request->date);

        $start_periode = $periode[0];
        $end_periode   = $periode[1];

        $absence = new Absence;

        $absence->name       = $request->name;
        $absence->date_start = date('Y-m-d', strtotime($start_periode));
        $absence->date_end   = date('Y-m-d', strtotime($end_periode));

        $absence->save();

        // get data from excel
        $data = '';
        if ($request->hasFile('excel'))
        {
            $data = Excel::load($request->file('excel')->getRealPath(), function ($reader) {})->get();
        
            if ( !empty($data) )
            {
                

                // grouping by sorted no._id and insert into AbsenceEmployee
                $return       = '';
                $init_machine = 0;
                foreach ($data as $list) {
                    if ($init_machine != $list['no._id'])
                    {
                        $init_machine = $list['no._id'];

                        $employee = Employee::join('shift', 'shift.id', 'employee.id_shift')
                            ->where('id_absence_machine', $init_machine)
                            ->select('employee.*', 'shift.day_per_month')->first();

                        if(!empty($employee))
                        {
                            $insert[] = [
                                'id_absence'           => $absence->id,
                                'id_employee'          => $employee->id,
                                'id_absence_machine'   => (int) $init_machine, 
                                'day_per_month'        => $employee->day_per_month ?? 1,
                                'gaji_pokok'           => $employee->gaji_pokok ,
                                'tunjangan'            => $employee->tunjangan,
                                'perawatan_motor'      => $employee->perawatan_motor,
                                'uang_makan'           => $employee->uang_makan,
                                'transport'            => $employee->transport,
                                'bpjs_kesehatan'       => $employee->bpjs_kesehatan,
                                'bpjs_ketenagakerjaan' => $employee->bpjs_ketenagakerjaan,
                                'uang_telat'           => $employee->uang_telat,
                                'uang_telat_permenit'  => $employee->uang_telat_permenit,
                                'uang_lembur'          => $employee->uang_lembur,
                                'uang_lembur_permenit' => $employee->uang_lembur_permenit,
                                'pph'                  => $employee->pph,
                            ];
                        }
                        else
                        {
                            $insert[] = [
                                'id_absence'           => $absence->id,
                                'id_employee'          => 0,
                                'id_absence_machine'   => (int) $init_machine, 
                                'day_per_month'        => 1,
                                'gaji_pokok'           => 0,
                                'tunjangan'            => 0,
                                'perawatan_motor'      => 0,
                                'uang_makan'           => 0,
                                'transport'            => 0,
                                'bpjs_kesehatan'       => 0,
                                'bpjs_ketenagakerjaan' => 0,
                                'uang_telat'           => 0,
                                'uang_telat_permenit'  => 1,
                                'uang_lembur'          => 0,
                                'uang_lembur_permenit' => 1,
                                'pph'                  => 0,
                            ];
                        }
                    }
                }

                // insert into AbsenceEmployeeDetail
                if ( !empty($insert) )
                {
                    AbsenceEmployee::insert($insert);

                    $absenceEmployee = AbsenceEmployee::where('id_absence', $absence->id)->get();

                    foreach ($absenceEmployee as $list)
                    {
                        
                        $start = $list->absence->date_start;

                        while ($start <= $list->absence->date_end)
                        {
                            $date[] = $start;
                            $start  = date('Y-m-d', strtotime($start . ' +1 day'));
                        }

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

                            $inserted_date[] = $format_date_php;

                            if ($list->id_machine == $list2['no._id']) {
                                $insert2[] = [
                                    'id_absence_employee' => $list->id,
                                    'date'                => $format_date_php,
                                    'shift_in'            => $list2['jam_masuk'] != '' ? $list2['jam_masuk'] : null,
                                    'shift_out'           => $list2['jam_pulang'] != '' ? $list2['jam_pulang'] : null,
                                    'check_in'            => $list2['scan_masuk'] != '' ? $list2['scan_masuk'] : null,
                                    'check_out'           => $list2['scan_pulang'] != '' ? $list2['scan_pulang'] : null,
                                    'status'              => 'blank',
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
                                    'shift_in'            => null,
                                    'shift_out'           => null,
                                    'check_in'            => null,
                                    'check_out'           => null,
                                    'status'              => 'blank',
                                ];
                            }
                        }
                    }

                    if (!empty($insert2)) {
                        AbsenceEmployeeDetail::insert($insert2);
                    }
                }
            }
        }

        Session::flash('success', 'Data Has Been Added');
        return redirect()->route('admin.absence');
    }

    public function edit($id, Request $request)
    {
        $index = Absence::find($id);

        return view('backend.absence.edit')->with(compact('index', 'request'));
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

    public function delete(Request $request)
    {
        Absence::destroy($request->id);

        Session::flash('success', 'Data berhasil dihapus');
        return redirect()->back();
    }

    public function action(Request $request)
    {
        if (isset($request->id)) {
            if ($request->action == 'delete') {
                Absence::destroy($request->id);
                Session::flash('success', 'Data Dipilih berhasil dihapus');
            } else if ($request->action == 'enable') {
                $index = Absence::whereIn('id', $request->id)->update(['active' => 1]);
                Session::flash('success', 'Data Selected Has Been Actived');
            } else if ($request->action == 'disable') {
                $index = Absence::whereIn('id', $request->id)->update(['active' => 0]);
                Session::flash('success', 'Data Selected Has Been Inactived');
            }
        }

        return redirect()->back();
    }

    public function datatablesEmployee($id, Request $request)
    {
        $sql_point = '
            (
                SELECT
                    `absence_employee_detail`.`id_absence_employee`,
                    `absence_employee_detail`.`date`,
                    `absence_employee_detail`.`shift_in`,
                    `absence_employee_detail`.`shift_out`,
                    `absence_employee_detail`.`check_in`,
                    `absence_employee_detail`.`check_out`,
                    `absence_employee_detail`.`fine_additional`,
                    `holiday`.`type` AS `type_holiday`,
                    `holiday`.`name` AS `name_holiday`,
                    `dayoff`.`type` AS `type_dayoff`,
                    `overtime`.`end_overtime`,
                    (
                        CASE 
                            WHEN COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_in`,`absence_employee_detail`.`check_in`), 0) > 0
                            THEN @late := COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_in`,`absence_employee_detail`.`check_in`), 0)
                            ELSE @late := 0 
                        END
                    ) AS `minute_late`,
                    COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_out`,`absence_employee_detail`.`check_out`), 0) AS `minute_overtime`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`check_in` IS NOT NULL OR `absence_employee_detail`.`check_out` IS NOT NULL 
                            THEN 1 
                            ELSE 0 
                        END
                    ) AS `point_lunch`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`shift_in` IS NOT NULL 
                                AND `absence_employee_detail`.`shift_out` IS NOT NULL
                                AND `absence_employee_detail`.`check_in` IS NULL
                                AND `absence_employee_detail`.`check_out` IS NULL
                                AND `holiday`.`type` IS NULL
                                AND `dayoff`.`type` IS NULL
                            THEN 1 
                            ELSE 0 
                        END
                    ) AS `point_alpa`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`shift_in` IS NOT NULL 
                                AND `absence_employee_detail`.`shift_out` IS NOT NULL
                                AND (`absence_employee_detail`.`check_in` IS NULL XOR `absence_employee_detail`.`check_out` IS NULL)
                                AND `holiday`.`type` IS NULL
                                AND `dayoff`.`type` IS NULL
                            THEN 1 
                            ELSE 0 
                        END
                    ) AS `point_pending`,
                    (
                        CASE 
                            WHEN @late > 0
                            THEN FLOOR(@late / `absence_employee`.`uang_telat_permenit`) + 1
                            ELSE 0 
                        END
                    ) AS `point_late`,
                    (
                        CASE 
                            WHEN `employee`.`need_book_overtime` = 1
                            THEN (
                                CASE
                                    WHEN `overtime`.`check_leader` = 1
                                    THEN
                                        @least_overtime := STR_TO_DATE(LEAST(CONCAT(`absence_employee_detail`.`date`,\' \',`absence_employee_detail`.`check_out`), `overtime`.`end_overtime`), \'%Y-%m-%d %H:%i:%s\')
                                    ELSE
                                        @least_overtime := NULL
                                END
                            )
                            ELSE (
                                CASE
                                    WHEN TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_out`,`absence_employee_detail`.`check_out`) > 0
                                    THEN
                                        @least_overtime := STR_TO_DATE(CONCAT(`absence_employee_detail`.`date`,\' \',`absence_employee_detail`.`check_out`), \'%Y-%m-%d %H:%i:%s\')
                                    ELSE
                                        @least_overtime := NULL
                                END
                            )
                        END
                    ) AS `least_overtime`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`shift_in` IS NULL AND `absence_employee_detail`.`shift_out` IS NULL AND `absence_employee_detail`.`check_in` IS NOT NULL AND `absence_employee_detail`.`check_out` IS NOT NULL AND  @overtime >= `employee`.`min_overtime`
                            THEN (FLOOR(TIMESTAMPDIFF(MINUTE,`absence_employee_detail`.`check_in`,`absence_employee_detail`.`check_out`) / `absence_employee`.`uang_lembur_permenit`) / 4)
                            WHEN `absence_employee_detail`.`shift_in` IS NOT NULL AND `absence_employee_detail`.`shift_out` IS NOT NULL AND `absence_employee_detail`.`check_in` IS NOT NULL AND `absence_employee_detail`.`check_out` IS NOT NULL AND `holiday`.`type` IS NOT NULL
                            THEN (FLOOR(TIMESTAMPDIFF(MINUTE,`absence_employee_detail`.`check_in`,`absence_employee_detail`.`check_out`) / `absence_employee`.`uang_lembur_permenit`) / 4)
                            WHEN @least_overtime IS NOT NULL AND  @overtime >= `employee`.`min_overtime`
                            THEN (FLOOR(TIMESTAMPDIFF(MINUTE,CONCAT(`absence_employee_detail`.`date`,\' \',`absence_employee_detail`.`shift_out`), @least_overtime) / `absence_employee`.`uang_lembur_permenit`) / 4)
                            ELSE 0
                        END
                    ) AS `point_overtime`
                FROM `absence_employee_detail`
                INNER JOIN `absence_employee` ON `absence_employee`.`id` = `absence_employee_detail`.`id_absence_employee`
                INNER JOIN `employee` ON `employee`.`id` = `absence_employee`.`id_employee`
                LEFT JOIN `holiday` ON `holiday`.`date` = `absence_employee_detail`.`date`
                LEFT JOIN `overtime` ON `overtime`.`date` = `absence_employee_detail`.`date` 
                    AND `overtime`.`id_employee` = `absence_employee`.`id_employee` 
                    AND `overtime`.`check_leader` = 1
                LEFT JOIN `dayoff` ON `absence_employee_detail`.`date` >= `dayoff`.`start_dayoff`
                    AND `absence_employee_detail`.`date` <= `dayoff`.`end_dayoff`
                    AND `dayoff`.`id_employee` = `absence_employee`.`id_employee`
                    AND `dayoff`.`check_leader` = 1
                ORDER BY `absence_employee_detail`.`date` ASC
            ) `absence_point`
        ';


        $index = AbsenceEmployee::where('id_absence', $id)
            ->leftJoin('employee', 'employee.id', 'absence_employee.id_employee')
            ->leftJoin(DB::raw($sql_point), 'absence_employee.id', 'absence_point.id_absence_employee')
            ->select(
                'absence_employee.*',
                'employee.name',
                DB::raw('SUM(absence_point.minute_late) AS minute_late'),
                DB::raw('SUM(absence_point.minute_overtime) AS minute_overtime'),
                DB::raw('SUM(absence_point.point_lunch) AS point_lunch'),
                DB::raw('SUM(absence_point.point_alpa) AS point_alpa'),
                DB::raw('SUM(absence_point.point_pending) AS point_pending'),
                DB::raw('SUM(absence_point.point_late) AS point_late'),
                DB::raw('SUM(absence_point.point_overtime) AS point_overtime')
            )
            ->groupBy('absence_employee.id_employee')
            ->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('view-absence'))
            {
                $html .= '
                    <a href="' . route('admin.absence.editEmployee', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-eye"></i></a>
                ';
            }

            if(Auth::user()->can('delete-absence'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-absenceEmployee" data-toggle="modal" data-target="#delete-absenceEmployee" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            return $html;
        });

        $datatables->editColumn('name', function ($index) {
            if($index->name == '')
            {
                return 'id absen karyawan tidak dipasang ' . $index->id_absen_employee;
            }
            else
            {
                return $index->name;
            }
        });

        $datatables->editColumn('date', function ($index) {
            return date('d/m/Y', strtotime($index->date));
        });

        $datatables->editColumn('minute_late', function ($index) {
            return number_format($index->minute_late);
        });

        $datatables->editColumn('minute_overtime', function ($index) {
            return number_format($index->minute_overtime);
        });

        $datatables->editColumn('point_lunch', function ($index) {
            return number_format($index->point_lunch);
        });

        $datatables->editColumn('point_alpa', function ($index) {
            return number_format($index->point_alpa);
        });

        $datatables->editColumn('point_pending', function ($index) {
            return number_format($index->point_pending);
        });

        $datatables->editColumn('point_late', function ($index) {
            return number_format($index->point_late);
        });

        $datatables->editColumn('point_overtime', function ($index) {
            return number_format($index->point_overtime, 2);
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

    public function createEmployee($id)
    {
        $index    = Absence::find($id);
        $employee = Employee::all();

        return view('backend.absence.employee.create', compact('index', 'employee'));
    }

    public function storeEmployee($id, Request $request)
    {
        $this->validate($request, [
            'id_employee'          => 'required|integer',
            'day_per_month'        => 'required|integer',
            'gaji_pokok'           => 'required|numeric',
            'tunjangan'            => 'required|numeric',
            'perawatan_motor'      => 'required|numeric',
            'uang_makan'           => 'required|numeric',
            'transport'            => 'required|numeric',
            'bpjs_kesehatan'       => 'required|numeric',
            'bpjs_ketenagakerjaan' => 'required|numeric',
            'uang_telat'           => 'required|numeric',
            'uang_telat_permenit'  => 'required|integer',
            'uang_lembur'          => 'required|numeric',
            'uang_lembur_permenit' => 'required|integer',
            'pph'                  => 'required|numeric',
        ]);

        $index = new AbsenceEmployee;

        $index->id_absence           = $id;
        $index->id_employee          = $request->id_employee;
        $index->day_per_month        = $request->day_per_month;
        $index->gaji_pokok           = $request->gaji_pokok;
        $index->tunjangan            = $request->tunjangan;
        $index->perawatan_motor      = $request->perawatan_motor;
        $index->uang_makan           = $request->uang_makan;
        $index->transport            = $request->transport;
        $index->bpjs_kesehatan       = $request->bpjs_kesehatan;
        $index->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
        $index->uang_telat           = $request->uang_telat;
        $index->uang_telat_permenit  = $request->uang_telat_permenit;
        $index->uang_lembur          = $request->uang_lembur;
        $index->uang_lembur_permenit = $request->uang_lembur_permenit;
        $index->pph                  = $request->pph;

        $index->save();

        return redirect()->route('admin.absence.edit', [$id])->with('success', 'Data berhasil ditambah');
    }

    public function editEmployee($id)
    {
        $sql_point = '
            (
                SELECT
                    `absence_employee_detail`.`id_absence_employee`,
                    `absence_employee_detail`.`date`,
                    `absence_employee_detail`.`shift_in`,
                    `absence_employee_detail`.`shift_out`,
                    `absence_employee_detail`.`check_in`,
                    `absence_employee_detail`.`check_out`,
                    `absence_employee_detail`.`fine_additional`,
                    `holiday`.`type` AS `type_holiday`,
                    `holiday`.`name` AS `name_holiday`,
                    `dayoff`.`type` AS `type_dayoff`,
                    `overtime`.`end_overtime`,
                    (
                        CASE 
                            WHEN COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_in`,`absence_employee_detail`.`check_in`), 0) > 0
                            THEN @late := COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_in`,`absence_employee_detail`.`check_in`), 0)
                            ELSE @late := 0 
                        END
                    ) AS `minute_late`,
                    COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_out`,`absence_employee_detail`.`check_out`), 0) AS `minute_overtime`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`check_in` IS NOT NULL OR `absence_employee_detail`.`check_out` IS NOT NULL 
                            THEN 1 
                            ELSE 0 
                        END
                    ) AS `point_lunch`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`shift_in` IS NOT NULL 
                                AND `absence_employee_detail`.`shift_out` IS NOT NULL
                                AND `absence_employee_detail`.`check_in` IS NULL
                                AND `absence_employee_detail`.`check_out` IS NULL
                                AND `holiday`.`type` IS NULL
                                AND `dayoff`.`type` IS NULL
                            THEN 1 
                            ELSE 0 
                        END
                    ) AS `point_alpa`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`shift_in` IS NOT NULL 
                                AND `absence_employee_detail`.`shift_out` IS NOT NULL
                                AND (`absence_employee_detail`.`check_in` IS NULL XOR `absence_employee_detail`.`check_out` IS NULL)
                                AND `holiday`.`type` IS NULL
                                AND `dayoff`.`type` IS NULL
                            THEN 1 
                            ELSE 0 
                        END
                    ) AS `point_pending`,
                    (
                        CASE 
                            WHEN @late > 0
                            THEN FLOOR(@late / `absence_employee`.`uang_telat_permenit`) + 1
                            ELSE 0 
                        END
                    ) AS `point_late`,
                    (
                        CASE 
                            WHEN `employee`.`need_book_overtime` = 1
                            THEN (
                                CASE
                                    WHEN `overtime`.`check_leader` = 1
                                    THEN
                                        @least_overtime := STR_TO_DATE(LEAST(CONCAT(`absence_employee_detail`.`date`,\' \',`absence_employee_detail`.`check_out`), `overtime`.`end_overtime`), \'%Y-%m-%d %H:%i:%s\')
                                    ELSE
                                        @least_overtime := NULL
                                END
                            )
                            ELSE (
                                CASE
                                    WHEN TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_out`,`absence_employee_detail`.`check_out`) > 0
                                    THEN
                                        @least_overtime := STR_TO_DATE(CONCAT(`absence_employee_detail`.`date`,\' \',`absence_employee_detail`.`check_out`), \'%Y-%m-%d %H:%i:%s\')
                                    ELSE
                                        @least_overtime := NULL
                                END
                            )
                        END
                    ) AS `least_overtime`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`shift_in` IS NULL AND `absence_employee_detail`.`shift_out` IS NULL AND `absence_employee_detail`.`check_in` IS NOT NULL AND `absence_employee_detail`.`check_out` IS NOT NULL AND  @overtime >= `employee`.`min_overtime`
                            THEN (FLOOR(TIMESTAMPDIFF(MINUTE,`absence_employee_detail`.`check_in`,`absence_employee_detail`.`check_out`) / `absence_employee`.`uang_lembur_permenit`) / 4)
                            WHEN `absence_employee_detail`.`shift_in` IS NOT NULL AND `absence_employee_detail`.`shift_out` IS NOT NULL AND `absence_employee_detail`.`check_in` IS NOT NULL AND `absence_employee_detail`.`check_out` IS NOT NULL AND `holiday`.`type` IS NOT NULL
                            THEN (FLOOR(TIMESTAMPDIFF(MINUTE,`absence_employee_detail`.`check_in`,`absence_employee_detail`.`check_out`) / `absence_employee`.`uang_lembur_permenit`) / 4)
                            WHEN @least_overtime IS NOT NULL AND  @overtime >= `employee`.`min_overtime`
                            THEN (FLOOR(TIMESTAMPDIFF(MINUTE,CONCAT(`absence_employee_detail`.`date`,\' \',`absence_employee_detail`.`shift_out`), @least_overtime) / `absence_employee`.`uang_lembur_permenit`) / 4)
                            ELSE 0
                        END
                    ) AS `point_overtime`
                FROM `absence_employee_detail`
                INNER JOIN `absence_employee` ON `absence_employee`.`id` = `absence_employee_detail`.`id_absence_employee`
                INNER JOIN `employee` ON `employee`.`id` = `absence_employee`.`id_employee`
                LEFT JOIN `holiday` ON `holiday`.`date` = `absence_employee_detail`.`date`
                LEFT JOIN `overtime` ON `overtime`.`date` = `absence_employee_detail`.`date` 
                    AND `overtime`.`id_employee` = `absence_employee`.`id_employee` 
                    AND `overtime`.`check_leader` = 1
                LEFT JOIN `dayoff` ON `absence_employee_detail`.`date` >= `dayoff`.`start_dayoff`
                    AND `absence_employee_detail`.`date` <= `dayoff`.`end_dayoff`
                    AND `dayoff`.`id_employee` = `absence_employee`.`id_employee`
                    AND `dayoff`.`check_leader` = 1
                ORDER BY `absence_employee_detail`.`date` ASC
            ) `absence_point`
        ';

        $index = AbsenceEmployee::where('absence_employee.id', $id)
            ->leftJoin('employee', 'employee.id', 'absence_employee.id_employee')
            ->leftJoin(DB::raw($sql_point), 'absence_employee.id', 'absence_point.id_absence_employee')
            ->select(
                'absence_employee.*',
                'employee.name',
                DB::raw('SUM(absence_point.minute_late) AS minute_late'),
                DB::raw('SUM(absence_point.minute_overtime) AS minute_overtime'),
                DB::raw('SUM(absence_point.point_lunch) AS point_lunch'),
                DB::raw('SUM(absence_point.point_alpa) AS point_alpa'),
                DB::raw('SUM(absence_point.point_pending) AS point_pending'),
                DB::raw('SUM(absence_point.point_late) AS point_late'),
                DB::raw('SUM(absence_point.point_overtime) AS point_overtime'),
                DB::raw('SUM(absence_point.fine_additional) AS fine_additional')
            )
            ->groupBy('absence_employee.id_employee')
            ->first();

        $employee = Employee::all();

        return view('backend.absence.employee.edit')->with(compact('index', 'employee'));
    }

    public function updateEmployee($id, Request $request)
    {
        $this->validate($request, [
            'id_employee'          => 'required|integer',
            'day_per_month'        => 'required|integer',
            'gaji_pokok'           => 'required|numeric',
            'tunjangan'            => 'required|numeric',
            'perawatan_motor'      => 'required|numeric',
            'uang_makan'           => 'required|numeric',
            'transport'            => 'required|numeric',
            'bpjs_kesehatan'       => 'required|numeric',
            'bpjs_ketenagakerjaan' => 'required|numeric',
            'uang_telat'           => 'required|numeric',
            'uang_telat_permenit'  => 'required|integer',
            'uang_lembur'          => 'required|numeric',
            'uang_lembur_permenit' => 'required|integer',
            'pph'                  => 'required|numeric',
        ]);

        $index = AbsenceEmployee::find($id);

        $index->id_employee          = $request->id_employee;
        $index->day_per_month        = $request->day_per_month;
        $index->gaji_pokok           = $request->gaji_pokok;
        $index->tunjangan            = $request->tunjangan;
        $index->perawatan_motor      = $request->perawatan_motor;
        $index->uang_makan           = $request->uang_makan;
        $index->transport            = $request->transport;
        $index->bpjs_kesehatan       = $request->bpjs_kesehatan;
        $index->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
        $index->uang_telat           = $request->uang_telat;
        $index->uang_telat_permenit  = $request->uang_telat_permenit;
        $index->uang_lembur          = $request->uang_lembur;
        $index->uang_lembur_permenit = $request->uang_lembur_permenit;
        $index->pph                  = $request->pph;
        
        $index->save();

        return redirect()->route('admin.absence.editEmployee', [$id])->with('success', 'Data berhasil diubah');
    }

    public function deleteEmployee(Request $request)
    {
        AbsenceEmployee::destroy($request->id);

        Session::flash('success', 'Data berhasil dihapus');
        return redirect()->back();
    }

    public function actionEmployee(Request $request)
    {
        if (isset($request->id)) {
            if ($request->action == 'delete') {
                AbsenceEmployee::destroy($request->id);
                Session::flash('success', 'Data Dipilih berhasil dihapus');
            } else if ($request->action == 'enable') {
                $index = AbsenceEmployee::whereIn('id', $request->id)->update(['active' => 1]);
                Session::flash('success', 'Data Selected Has Been Actived');
            } else if ($request->action == 'disable') {
                $index = AbsenceEmployee::whereIn('id', $request->id)->update(['active' => 0]);
                Session::flash('success', 'Data Selected Has Been Inactived');
            }
        }

        return redirect()->back();
    }

    public function datatablesEmployeeDetail($id, Request $request)
    {
        $sql_point = '
            (
                SELECT
                    `absence_employee_detail`.`id`,
                    `absence_employee_detail`.`date`,
                    `absence_employee_detail`.`shift_in`,
                    `absence_employee_detail`.`shift_out`,
                    `absence_employee_detail`.`check_in`,
                    `absence_employee_detail`.`check_out`,
                    `absence_employee_detail`.`fine_additional`,
                    `holiday`.`type` AS `type_holiday`,
                    `holiday`.`name` AS `name_holiday`,
                    `dayoff`.`type` AS `type_dayoff`,
                    `overtime`.`end_overtime`,
                    (
                        CASE 
                            WHEN COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_in`,`absence_employee_detail`.`check_in`), 0) > 0
                            THEN @late := COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_in`,`absence_employee_detail`.`check_in`), 0)
                            ELSE @late := 0 
                        END
                    ) AS `minute_late`,
                    COALESCE(TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_out`,`absence_employee_detail`.`check_out`), 0) AS `minute_overtime`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`check_in` IS NOT NULL OR `absence_employee_detail`.`check_out` IS NOT NULL 
                            THEN 1 
                            ELSE 0 
                        END
                    ) AS `point_lunch`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`shift_in` IS NOT NULL 
                                AND `absence_employee_detail`.`shift_out` IS NOT NULL
                                AND `absence_employee_detail`.`check_in` IS NULL
                                AND `absence_employee_detail`.`check_out` IS NULL
                                AND `holiday`.`type` IS NULL
                                AND `dayoff`.`type` IS NULL
                            THEN 1 
                            ELSE 0 
                        END
                    ) AS `point_alpa`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`shift_in` IS NOT NULL 
                                AND `absence_employee_detail`.`shift_out` IS NOT NULL
                                AND (`absence_employee_detail`.`check_in` IS NULL XOR `absence_employee_detail`.`check_out` IS NULL)
                                AND `holiday`.`type` IS NULL
                                AND `dayoff`.`type` IS NULL
                            THEN 1 
                            ELSE 0 
                        END
                    ) AS `point_pending`,
                    (
                        CASE 
                            WHEN @late > 0
                            THEN FLOOR(@late / `absence_employee`.`uang_telat_permenit`) + 1
                            ELSE 0 
                        END
                    ) AS `point_late`,
                    (
                        CASE 
                            WHEN `employee`.`need_book_overtime` = 1
                            THEN (
                                CASE
                                    WHEN `overtime`.`check_leader` = 1
                                    THEN
                                        @least_overtime := STR_TO_DATE(LEAST(CONCAT(`absence_employee_detail`.`date`,\' \',`absence_employee_detail`.`check_out`), `overtime`.`end_overtime`), \'%Y-%m-%d %H:%i:%s\')
                                    ELSE
                                        @least_overtime := NULL
                                END
                            )
                            ELSE (
                                CASE
                                    WHEN TIMESTAMPDIFF(MINUTE, `absence_employee_detail`.`shift_out`,`absence_employee_detail`.`check_out`) > 0
                                    THEN
                                        @least_overtime := STR_TO_DATE(CONCAT(`absence_employee_detail`.`date`,\' \',`absence_employee_detail`.`check_out`), \'%Y-%m-%d %H:%i:%s\')
                                    ELSE
                                        @least_overtime := NULL
                                END
                            )
                        END
                    ) AS `least_overtime`,
                    (
                        CASE 
                            WHEN `absence_employee_detail`.`shift_in` IS NULL AND `absence_employee_detail`.`shift_out` IS NULL AND `absence_employee_detail`.`check_in` IS NOT NULL AND `absence_employee_detail`.`check_out` IS NOT NULL AND  @overtime >= `employee`.`min_overtime`
                            THEN (FLOOR(TIMESTAMPDIFF(MINUTE,`absence_employee_detail`.`check_in`,`absence_employee_detail`.`check_out`) / `absence_employee`.`uang_lembur_permenit`) / 4)
                            WHEN `absence_employee_detail`.`shift_in` IS NOT NULL AND `absence_employee_detail`.`shift_out` IS NOT NULL AND `absence_employee_detail`.`check_in` IS NOT NULL AND `absence_employee_detail`.`check_out` IS NOT NULL AND `holiday`.`type` IS NOT NULL
                            THEN (FLOOR(TIMESTAMPDIFF(MINUTE,`absence_employee_detail`.`check_in`,`absence_employee_detail`.`check_out`) / `absence_employee`.`uang_lembur_permenit`) / 4)
                            WHEN @least_overtime IS NOT NULL AND  @overtime >= `employee`.`min_overtime`
                            THEN (FLOOR(TIMESTAMPDIFF(MINUTE,CONCAT(`absence_employee_detail`.`date`,\' \',`absence_employee_detail`.`shift_out`), @least_overtime) / `absence_employee`.`uang_lembur_permenit`) / 4)
                            ELSE 0
                        END
                    ) AS `point_overtime`
                FROM `absence_employee_detail`
                INNER JOIN `absence_employee` ON `absence_employee`.`id` = `absence_employee_detail`.`id_absence_employee`
                INNER JOIN `employee` ON `employee`.`id` = `absence_employee`.`id_employee`
                LEFT JOIN `holiday` ON `holiday`.`date` = `absence_employee_detail`.`date`
                LEFT JOIN `overtime` ON `overtime`.`date` = `absence_employee_detail`.`date` 
                    AND `overtime`.`id_employee` = `absence_employee`.`id_employee` 
                    AND `overtime`.`check_leader` = 1
                LEFT JOIN `dayoff` ON `absence_employee_detail`.`date` >= `dayoff`.`start_dayoff`
                    AND `absence_employee_detail`.`date` <= `dayoff`.`end_dayoff`
                    AND `dayoff`.`id_employee` = `absence_employee`.`id_employee`
                    AND `dayoff`.`check_leader` = 1
                ORDER BY `absence_employee_detail`.`date` ASC
            ) `absence_point`
        ';

        $index = AbsenceEmployeeDetail::where('id_absence_employee', $id)
            ->select('absence_point.*')
            ->leftJoin(DB::raw($sql_point), 'absence_employee_detail.id', 'absence_point.id')
            ->orderBy('absence_employee_detail.date', 'ASC')
            ->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('view-absence'))
            {
                $html .= '
                    <a href="' . route('admin.absence.editEmployeeDetail', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('delete-absence'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-absenceEmployeeDetail" data-toggle="modal" data-target="#delete-absenceEmployeeDetail" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            return $html;
        });

        $datatables->editColumn('type_holiday', function ($index) {
            $html = '';

            if($index->type_holiday != '')
            {
                $html .= $index->type_holiday . ' : ' . $index->name_holiday;
            }

            return $html;
        });

        $datatables->editColumn('date', function ($index) {
            return date('d/m/Y', strtotime($index->date));
        });

        $datatables->editColumn('minute_late', function ($index) {
            return number_format($index->minute_late);
        });

        $datatables->editColumn('minute_overtime', function ($index) {
            return number_format($index->minute_overtime);
        });

        $datatables->editColumn('point_lunch', function ($index) {
            return number_format($index->point_lunch);
        });

        $datatables->editColumn('point_alpa', function ($index) {
            return number_format($index->point_alpa);
        });

        $datatables->editColumn('point_pending', function ($index) {
            return number_format($index->point_pending);
        });

        $datatables->editColumn('point_late', function ($index) {
            return number_format($index->point_late);
        });

        $datatables->editColumn('point_overtime', function ($index) {
            return number_format($index->point_overtime, 2);
        });

        $datatables->editColumn('fine_additional', function ($index) {
            return number_format($index->fine_additional);
        });

        $datatables->editColumn('shift_in', function ($index) {
            $html = '';
            if($index->shift_in != '')
            {
                $html .= date('H:i', strtotime($index->shift_in));
            }

            return $html;
        });

        $datatables->editColumn('shift_out', function ($index) {
            $html = '';
            if($index->shift_out != '')
            {
                $html .= date('H:i', strtotime($index->shift_out));
            }

            return $html;
        });

        $datatables->editColumn('check_in', function ($index) {
            $html = '';
            if($index->check_in != '')
            {
                $html .= date('H:i', strtotime($index->check_in));
            }

            return $html;
        });

        $datatables->editColumn('check_out', function ($index) {
            $html = '';
            if($index->check_out != '')
            {
                $html .= date('H:i', strtotime($index->check_out));
            }

            return $html;
        });

        $datatables->editColumn('end_overtime', function ($index) {
            $html = '';
            if($index->end_overtime != '')
            {
                $html .= date('d/m/Y H:i', strtotime($index->end_overtime));
            }

            return $html;
        });

        $datatables->editColumn('fine_additional', function ($index) {
            return number_format($index->fine_additional);
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

    public function createEmployeeDetail($id)
    {
        $index = AbsenceEmployee::find($id);
        return view('backend.absence.employee.detail.create')->with(compact('index'));
    }

    public function storeEmployeeDetail($id, Request $request)
    {
        $this->validate($request, [
            'date'            => 'required|date',
            'shift_in'        => 'nullable',
            'shift_out'       => 'nullable',
            'check_in'        => 'nullable',
            'check_out'       => 'nullable',
            // 'status'          => 'required',
            'fine_additional' => 'nullable|numeric',
        ]);

        $index = new AbsenceEmployeeDetail;

        $index->id_absence_employee = $id;
        $index->date                = date('Y-m-d', strtotime($request->date));
        $index->shift_in            = $request->shift_in != '' ? date('H:i:s', strtotime($request->shift_in)) : null;
        $index->shift_out           = $request->shift_out != '' ? date('H:i:s', strtotime($request->shift_out)) : null;
        $index->check_in            = $request->check_in != '' ? date('H:i:s', strtotime($request->check_in)) : null;
        $index->check_out           = $request->check_out != '' ? date('H:i:s', strtotime($request->check_out)) : null;
        $index->fine_additional     = $request->fine_additional;

        $index->save();

        return redirect()->route('admin.absence.editEmployee', ['id' => $id])->with('success', 'Data has been added.');
    }

    public function editEmployeeDetail($id)
    {
        $index = AbsenceEmployeeDetail::find($id);

        $absence_employee = AbsenceEmployee::where('id', $index->id_absence_employee)->first();

        return view('backend.absence.employee.detail.edit')->with(compact('index', 'absence_employee'));
    }

    public function updateEmployeeDetail($id, Request $request)
    {
        $this->validate($request, [
            'date'            => 'required|date',
            'shift_in'        => 'nullable',
            'shift_out'       => 'nullable',
            'check_in'        => 'nullable',
            'check_out'       => 'nullable',
            'fine_additional' => 'nullable|numeric',
        ]);

        $index = AbsenceEmployeeDetail::find($request->id);

        $index->date            = date('Y-m-d', strtotime($request->date));
        $index->shift_in        = $request->shift_in != '' ? date('H:i:s', strtotime($request->shift_in)) : null;
        $index->shift_out       = $request->shift_out != '' ? date('H:i:s', strtotime($request->shift_out)) : null;
        $index->check_in        = $request->check_in != '' ? date('H:i:s', strtotime($request->check_in)) : null;
        $index->check_out       = $request->check_out != '' ? date('H:i:s', strtotime($request->check_out)) : null;
        $index->fine_additional = $request->fine_additional;

        $index->save();

        return redirect()->route('admin.absence.editEmployee', ['id' => $index->id_absence_employee])->with('success', 'Data has been added.');
    }

    public function deleteEmployeeDetail(Request $request)
    {
        AbsenceEmployeeDetail::destroy($request->id);

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    public function actionEmployeeDetail(Request $request)
    {
        if (isset($request->id)) {
            if ($request->action == 'delete') {
                AbsenceEmployeeDetail::destroy($request->id);
                Session::flash('success', 'Data Dipilih berhasil dihapus');
            } else if ($request->action == 'enable') {
                $index = AbsenceEmployeeDetail::whereIn('id', $request->id)->update(['active' => 1]);
                Session::flash('success', 'Data Selected Has Been Actived');
            } else if ($request->action == 'disable') {
                $index = AbsenceEmployeeDetail::whereIn('id', $request->id)->update(['active' => 0]);
                Session::flash('success', 'Data Selected Has Been Inactived');
            }
        }

        return redirect()->back();
    }

    public function ajaxPayroll(Request $request)
    {
        $index = Employee::join('shift', 'shift.id', 'employee.id_shift')->select('gaji_pokok', 'tunjangan', 'perawatan_motor', 'uang_makan', 'transport', 'bpjs_kesehatan', 'bpjs_ketenagakerjaan', 'uang_telat', 'uang_telat_permenit', 'uang_lembur', 'uang_lembur_permenit', 'pph', 'day_per_month')->where('employee.id', $request->id_employee)->first();

        return $index;
    }

    public function ajaxShift(Request $request)
    {
        $index = Employee::join('shift', 'shift.id', 'employee.id_shift')->join('shift_detail', 'shift.id', 'shift_detail.id_shift')->select('shift_in', 'shift_out', 'day')->where('employee.id', $request->id_employee)->where('day', date('w', strtotime($request->date)))->first();

        return $index;
    }

}
