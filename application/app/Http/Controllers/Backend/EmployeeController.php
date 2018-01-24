<?php

namespace App\Http\Controllers\Backend;

use App\Employee;
use App\EmployeeFamily;
use App\Contract;
use App\Payroll;
use App\Shift;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Session;
use Datatables;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('backend.employee.index')->with(compact('request'));
    }

    public function datatables(Request $request)
    {
        $f_status  = $this->filter($request->f_status);

        $index = Employee::select('employee.*');

        if($f_status != '' && $f_status == 'active')
        {
            $index->whereDate('date_resign', '>', date('Y-m-d'))->orwhereNull('date_resign');
        }
        else if($f_status != '' && $f_status == 'resign')
        {
            $index->whereNotNull('date_resign');
        }

        $index = $index->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('view-employee'))
            {
                $html .= '
                    <a href="' . route('admin.employee.edit', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-eye"></i></a>
                ';
            }

            if(Auth::user()->can('delete-employee'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger delete-employee" data-toggle="modal" data-target="#delete-employee" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            return $html;
        });

        $datatables->editColumn('date_join', function ($index) {
            return date('d/m/Y', strtotime($index->date_join));
        });

        $datatables->editColumn('date_resign', function ($index) {
            $html = '';
            if($index->date_resign)
            {
                $html .= '
                    <span class="label label-warning">Resign tgl ' . date('d/m/Y', strtotime($index->date_resign)) . '</span>
                ';
            }
            else
            {
                $html .= '
                    <span class="label label-info">Aktif</span>
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
        $headEmployee = Employee::where('level', 'leader')->whereNull('date_resign')->get();
        $shift = Shift::all();

        return view('backend.employee.create')->with(compact('headEmployee', 'shift'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name'            => 'required',
            'birthday'        => 'required|date',
            'gender'          => 'required',
            'religion'        => 'required',
            'no_ktp'          => 'required',
            'status'          => 'required',
            'ktp_address'     => 'required',
            'current_address' => 'required',
            'npwp'            => 'nullable',
            'phone'           => 'required',

            'date_join'          => 'required|date',
            'nik'                => 'required|unique:employee,nik',
            'job_title'          => 'required',
            'division'           => 'nullable',
            'sub_division'       => 'nullable',
            'level'              => 'required',
            'id_leader'          => 'required_if:level,atasan|integer',
            'id_absence_machine' => 'nullable|integer',

            'type_contract'       => 'required',
            'start_date_contract' => 'required|date',
            'end_date_contract'   => 'required|date',
            'id_shift'            => 'required|integer',
            'min_overtime'        => 'required|integer',
            'guarantee'           => 'nullable',

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
            'update_payroll'       => 'required|date',

            'test_disc'   => 'nullable',
            'test_gratyo' => 'nullable',
            'test_math'   => 'nullable',

            'emergency_name'     => 'nullable',
            'emergency_phone'    => 'nullable',
            'emergency_relation' => 'nullable',

            'date_resign'   => 'nullable|date',
        ]);

        $index = new Employee;

        $index->name            = $request->name;
        $index->birthday        = date('Y-m-d', strtotime($request->birthday));
        $index->gender          = $request->gender;
        $index->religion        = $request->religion;
        $index->no_ktp          = $request->no_ktp;
        $index->status          = $request->status;
        $index->ktp_address     = $request->ktp_address;
        $index->current_address = $request->current_address;
        $index->npwp            = $request->npwp;
        $index->npwp_address    = $request->npwp_address;
        $index->npwp_status     = $request->npwp_status;
        $index->phone           = $request->phone;
        
        $index->date_join          = date('Y-m-d', strtotime($request->date_join));
        $index->nik                = $request->nik;
        $index->job_title          = $request->job_title;
        $index->division           = $request->division;
        $index->sub_division       = $request->sub_division;
        $index->level              = $request->level;
        $index->id_leader          = $request->id_leader;
        $index->id_absence_machine = $request->id_absence_machine;

        $index->type_contract       = $request->type_contract;
        $index->start_date_contract = date('Y-m-d', strtotime($request->start_date_contract));
        $index->end_date_contract   = date('Y-m-d', strtotime($request->end_date_contract));
        $index->id_shift            = $request->id_shift;
        $index->need_book_overtime  = isset($request->need_book_overtime) ? 1 : 0;
        $index->min_overtime        = $request->min_overtime;
        $index->guarantee           = $request->guarantee;
        $index->status_guarantee    = isset($request->status_guarantee) ? 1 : 0;

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
        $index->update_payroll       = date('Y-m-d', strtotime($request->update_payroll));

        $index->test_disc   = $request->test_disc;
        $index->test_gratyo = $request->test_gratyo;
        $index->test_math   = $request->test_math;

        $index->emergency_name     = $request->emergency_name;
        $index->emergency_phone    = $request->emergency_phone;
        $index->emergency_relation = $request->emergency_relation;

        $index->status_resign = isset($request->status_resign) ? 1 : 0;
        $index->date_resign   = $request->date_resign != '' ? date('Y-m-d', strtotime($request->date_resign)) : null;

        $index->save();

        $contract = new Contract;

        $contract->id_employee         = $index->id;
        $contract->type_contract       = $request->type_contract;
        $contract->start_date_contract = date('Y-m-d', strtotime($request->start_date_contract));
        $contract->end_date_contract   = date('Y-m-d', strtotime($request->end_date_contract));
        $contract->id_shift            = $request->id_shift;
        $contract->need_book_overtime  = isset($request->need_book_overtime) ? 1 : 0;
        $contract->min_overtime        = $request->min_overtime;
        $contract->guarantee           = $request->guarantee;
        $contract->note                = 'Pertama';
        $contract->date_change         = date('Y-m-d');

        $contract->save();

        $payroll = new Payroll;

        $payroll->id_employee          = $index->id;
        $payroll->gaji_pokok           = $request->gaji_pokok;
        $payroll->tunjangan            = $request->tunjangan;
        $payroll->perawatan_motor      = $request->perawatan_motor;
        $payroll->uang_makan           = $request->uang_makan;
        $payroll->transport            = $request->transport;
        $payroll->bpjs_kesehatan       = $request->bpjs_kesehatan;
        $payroll->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
        $payroll->uang_telat           = $request->uang_telat;
        $payroll->uang_telat_permenit  = $request->uang_telat_permenit;
        $payroll->uang_lembur          = $request->uang_lembur;
        $payroll->uang_lembur_permenit = $request->uang_lembur_permenit;
        $payroll->pph                  = $request->pph;
        $payroll->update_payroll       = date('Y-m-d', strtotime($request->update_payroll));
        $payroll->note                 = 'Pertama';
        $payroll->date_change          = date('Y-m-d');

        $payroll->save();

        Session::flash('success', 'Data berhasil ditambah');
        return redirect()->route('admin.employee.edit', ['id' => $index->id]);
    }

    public function edit($id)
    {
        $index        = Employee::find($id);
        $headEmployee = Employee::where('level', 'leader')->whereNull('date_resign')->get();
        $shift = Shift::all();

        return view('backend.employee.edit')->with(compact('index', 'headEmployee', 'shift'));
    }

    public function update($id, $type, Request $request)
    {
        $index = Employee::find($id);

        if ($type == 'biodata') {
            $this->validate($request, [
                'name'            => 'required',
                'birthday'        => 'required|date',
                'gender'          => 'required',
                'religion'        => 'required',
                'no_ktp'          => 'required',
                'status'          => 'required',
                'ktp_address'     => 'required',
                'current_address' => 'required',
                'npwp'            => 'nullable',
                'phone'           => 'required',
            ]);

            $index->name            = $request->name;
            $index->birthday        = date('Y-m-d', strtotime($request->birthday));
            $index->gender          = $request->gender;
            $index->religion        = $request->religion;
            $index->no_ktp          = $request->no_ktp;
            $index->status          = $request->status;
            $index->ktp_address     = $request->ktp_address;
            $index->current_address = $request->current_address;
            $index->npwp            = $request->npwp;
            $index->npwp_address    = $request->npwp_address;
            $index->npwp_status     = $request->npwp_status;
            $index->phone           = $request->phone;

        } else if ($type == 'data') {
            $this->validate($request, [
                'date_join'          => 'required|date',
                'nik'                => 'required|unique:employee,nik',
                'job_title'          => 'required',
                'division'           => 'nullable',
                'sub_division'       => 'nullable',
                'level'              => 'required',
                'id_leader'          => 'required_if:level,atasan|integer',
                'id_absence_machine' => 'nullable|integer',
            ]);

            $index->date_join          = date('Y-m-d', strtotime($request->date_join));
            $index->nik                = $request->nik;
            $index->job_title          = $request->job_title;
            $index->division           = $request->division;
            $index->sub_division       = $request->sub_division;
            $index->level              = $request->level;
            $index->id_leader          = $request->id_leader;
            $index->id_absence_machine = $request->id_absence_machine;

        } else if ($type == 'contract') {
            $this->validate($request, [
                'type_contract'        => 'required',
                'start_date_contract'  => 'required|date',
                'end_date_contract'    => 'required|date',
                'id_shift'             => 'required|integer',
                'min_overtime'         => 'required|integer',
                'guarantee'            => 'nullable',
                'note_contract'        => 'required',
                'date_change_contract' => 'required|date',
            ]);

            $contract = new Contract;

            $contract->id_employee         = $id;
	        $contract->type_contract       = $request->type_contract;
            $contract->start_date_contract = date('Y-m-d', strtotime($request->start_date_contract));
            $contract->end_date_contract   = date('Y-m-d', strtotime($request->end_date_contract));
            $contract->id_shift            = $request->id_shift;
            $contract->need_book_overtime  = isset($request->need_book_overtime) ? 1 : 0;
            $contract->min_overtime        = $request->min_overtime;
            $contract->guarantee           = $request->guarantee;
            $contract->note                = $request->note_contract;
            $contract->date_change         = date('Y-m-d', strtotime($request->date_change_contract));


            $contract->save();

            $index->type_contract       = $request->type_contract;
            $index->start_date_contract = date('Y-m-d', strtotime($request->start_date_contract));
            $index->end_date_contract   = date('Y-m-d', strtotime($request->end_date_contract));
            $index->id_shift            = $request->id_shift;
            $index->need_book_overtime  = isset($request->need_book_overtime) ? 1 : 0;
            $index->min_overtime        = $request->min_overtime;
            $index->guarantee           = $request->guarantee;
            $index->status_guarantee    = isset($request->status_guarantee) ? 1 : 0;

        } else if ($type == 'payroll') {
            $this->validate($request, [
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
                'update_payroll'       => 'required|date',
                'note_payroll'         => 'required',
                'date_change_payroll'  => 'required|date',
            ]);

            $payroll = new Payroll;

            $payroll->id_employee          = $id;
            $payroll->gaji_pokok           = $request->gaji_pokok;
            $payroll->tunjangan            = $request->tunjangan;
            $payroll->perawatan_motor      = $request->perawatan_motor;
            $payroll->uang_makan           = $request->uang_makan;
            $payroll->transport            = $request->transport;
            $payroll->bpjs_kesehatan       = $request->bpjs_kesehatan;
            $payroll->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
            $payroll->uang_telat           = $request->uang_telat;
            $payroll->uang_telat_permenit  = $request->uang_telat_permenit;
            $payroll->uang_lembur          = $request->uang_lembur;
            $payroll->uang_lembur_permenit = $request->uang_lembur_permenit;
            $payroll->pph                  = $request->pph;
            $payroll->update_payroll       = date('Y-m-d', strtotime($request->update_payroll));
            $payroll->note                 = $request->note_payroll;
            $payroll->date_change          = date('Y-m-d', strtotime($request->date_change_payroll));

            $payroll->save();

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
            $index->update_payroll       = date('Y-m-d', strtotime($request->update_payroll));

        } else if ($type == 'test') {
            $index->test_disc   = $request->test_disc;
            $index->test_gratyo = $request->test_gratyo;
            $index->test_math   = $request->test_math;
        } else if ($type == 'emergency') {
            $index->emergency_name     = $request->emergency_name;
            $index->emergency_phone    = $request->emergency_phone;
            $index->emergency_relation = $request->emergency_relation;
        } else if ($type == 'resign') {
            $this->validate($request, [
                'date_resign'   => 'nullable|date',
            ]);

            $index->status_resign = isset($request->status_resign) ? 1 : 0;
            $index->date_resign = $request->date_resign != '' ? date('Y-m-d', strtotime($request->date_resign)) : null;
        }

        $index->save();

        Session::flash('success', 'Data berhasil diupdate');
        return redirect::back();
    }

    public function delete(Request $request)
    {
        Employee::destroy($request->id);

        Session::flash('success', 'Data berhasil dihapus');
        return redirect()->route('admin.employee');
    }

    public function action(Request $request)
    {
        if ($request->action == 'delete') {
            Employee::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
        } else if ($request->action == 'enable') {
            $index = Employee::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
        } else if ($request->action == 'disable') {
            $index = Employee::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
        }

        return redirect()->route('admin.employee');
    }

    public function active($id, $action)
    {
        $index = Employee::find($id);

        $index->active = $action;

        $index->save();

        if ($action == 1) {
            Session::flash('success', 'Data Has Been Actived');
        } else {
            Session::flash('success', 'Data Has Been Inactived');
        }

        return redirect()->route('admin.employee');
    }

    public function datatablesFamily($id)
    {
        $index = EmployeeFamily::where('id_employee', $id)->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('editFamily-employee'))
            {
                $html .= '
                    <a href="' . route('admin.employee.editFamily', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('deleteFamily-employee'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger deleteFamily-employee" data-toggle="modal" data-target="#deleteFamily-employee" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            return $html;
        });

        $datatables->addColumn('check', function ($index) {
            $html = '';

            $html .= '
                <input type="checkbox" class="check-family" value="' . $index->id . '" name="id[]" form="action">
            ';

            return $html;
        });

        $datatables = $datatables->make(true);
        return $datatables;
    }

    public function createFamily($id)
    {
        $employee = Employee::find($id);

        return view('backend.employee.family.create')->with(compact('employee'));
    }

    public function storeFamily(Request $request, $id)
    {

        $this->validate($request, [
            'name'     => 'required',
            'relation' => 'required',
            'age'      => 'integer|nullable',
        ]);

        $index = new EmployeeFamily;

        $index->id_employee = $id;
        $index->relation    = $request->relation;
        $index->name        = $request->name;
        $index->age         = $request->age;
        $index->school      = $request->school;
        $index->job         = $request->job;

        $index->save();

        Session::flash('success', 'Data berhasil ditambah');
        return redirect()->route('admin.employee.edit', ['id' => $id]);
    }

    public function editFamily($id)
    {
        $index = EmployeeFamily::find($id);

        return view('backend.employee.family.edit')->with(compact('index'));
    }

    public function updateFamily($id, Request $request)
    {

        $this->validate($request, [
            'name'     => 'required',
            'relation' => 'required',
            'age'      => 'integer|nullable',
        ]);

        $index = EmployeeFamily::find($id);

        $index->relation = $request->relation;
        $index->name     = $request->name;
        $index->age      = $request->age;
        $index->school   = $request->school;
        $index->job      = $request->job;

        $index->save();

        Session::flash('success', 'Data berhasil diupdate');
        return redirect()->route('admin.employee.edit', ['id' => $index->id_employee]);
    }

    public function deleteFamily(Request $request)
    {
        EmployeeFamily::destroy($request->id);

        Session::flash('success', 'Data berhasil dihapus');
        return redirect()->back();
    }

    public function actionFamily(Request $request)
    {
        if ($request->action == 'delete') {
            EmployeeFamily::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
        } else if ($request->action == 'enable') {
            $index = EmployeeFamily::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
        } else if ($request->action == 'disable') {
            $index = EmployeeFamily::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
        }

        return redirect::back();
    }

    public function datatablesContract($id)
    {
        
        $index = Contract::where('id_employee', $id)->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('editContract-employee'))
            {
                $html .= '
                    <a href="' . route('admin.employee.editContract', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('deleteContract-employee'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger deleteContract-employee" data-toggle="modal" data-target="#deleteContract-employee" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            return $html;
        });

        $datatables->editColumn('start_date_contract', function ($index) {
            return date('d/m/Y', strtotime($index->start_date_contract));
        });

        $datatables->editColumn('end_date_contract', function ($index) {
            return date('d/m/Y', strtotime($index->end_date_contract));
        });

        $datatables->editColumn('date_change', function ($index) {
            return date('d/m/Y', strtotime($index->date_change));
        });

        $datatables->addColumn('check', function ($index) {
            $html = '';

            $html .= '
                <input type="checkbox" class="check-contract" value="' . $index->id . '" name="id[]" form="action">
            ';

            return $html;
        });

        $datatables = $datatables->make(true);
        return $datatables;
    }

    public function editContract($id)
    {
        $index = Contract::find($id);
        $shift = Shift::all();

        return view('backend.employee.contract.edit')->with(compact('index', 'shift'));
    }

    public function updateContract($id, Request $request)
    {
        $this->validate($request, [
            'type_contract'       => 'required',
            'start_date_contract' => 'required|date',
            'end_date_contract'   => 'required|date',
            'id_shift'            => 'required|integer',
            'need_book_overtime'  => 'required|boolean',
            'min_overtime'        => 'required|integer',
            'guarantee'           => 'nullable',
            'note'                => 'required',
            'date_change'         => 'required|date',
        ]);

        $index = Contract::find($id);

        $index->type_contract       = $request->type_contract;
        $index->start_date_contract = date('Y-m-d', strtotime($request->start_date_contract));
        $index->end_date_contract   = date('Y-m-d', strtotime($request->end_date_contract));
        $index->id_shift            = $request->id_shift;
        $index->need_book_overtime  = isset($request->need_book_overtime) ? 1 : 0;
        $index->min_overtime        = $request->min_overtime;
        $index->guarantee           = $request->guarantee;
        $index->note                = $request->note;
        $index->date_change         = date('Y-m-d', strtotime($request->date_change));

        $index->save();

        return redirect()->route('admin.employee.edit', ['id' => $index->id_employee])->with('success', 'Data berhasil diupdate');
    }

    public function deleteContract(Request $request)
    {
        Contract::destroy($request->id);

        Session::flash('success', 'Data berhasil dihapus');
        return redirect::back();
    }

    public function actionContract(Request $request)
    {
        if ($request->action == 'delete') {
            Contract::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
        } else if ($request->action == 'enable') {
            $index = Contract::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
        } else if ($request->action == 'disable') {
            $index = Contract::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
        }

        return redirect::back();
    }


    public function datatablesPayroll($id)
    {
        
        $index = Payroll::where('id_employee', $id)->get();

        $datatables = Datatables::of($index);

        $datatables->addColumn('action', function ($index) {
            $html = '';

            if(Auth::user()->can('editPayroll-employee'))
            {
                $html .= '
                    <a href="' . route('admin.employee.editPayroll', ['id' => $index->id]) . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                ';
            }

            if(Auth::user()->can('deletePayroll-employee'))
            {
                $html .= '
                    <button class="btn btn-xs btn-danger deletePayroll-employee" data-toggle="modal" data-target="#deletePayroll-employee" data-id="'.$index->id.'"><i class="fa fa-trash"></i></button>
                ';
            }

            return $html;
        });

        $datatables->editColumn('date_change', function ($index) {
            return date('d/m/Y', strtotime($index->date_change));
        });

        $datatables->editColumn('update_payroll', function ($index) {
            return date('d/m/Y', strtotime($index->update_payroll));
        });

        $datatables->editColumn('gaji_pokok', function ($index) {
            return number_format($index->gaji_pokok);
        });

        $datatables->editColumn('tunjangan', function ($index) {
            return number_format($index->tunjangan);
        });

        $datatables->editColumn('perawatan_motor', function ($index) {
            return number_format($index->perawatan_motor);
        });

        $datatables->editColumn('uang_makan', function ($index) {
            return number_format($index->uang_makan);
        });

        $datatables->editColumn('transport', function ($index) {
            return number_format($index->transport);
        });

        $datatables->editColumn('bpjs_kesehatan', function ($index) {
            return number_format($index->bpjs_kesehatan);
        });

        $datatables->editColumn('bpjs_ketenagakerjaan', function ($index) {
            return number_format($index->bpjs_ketenagakerjaan);
        });

        $datatables->editColumn('pph', function ($index) {
            return number_format($index->pph);
        });

        $datatables->editColumn('uang_telat', function ($index) {
            return number_format($index->uang_telat);
        });

        $datatables->editColumn('uang_lembur', function ($index) {
            return number_format($index->uang_lembur);
        });

        $datatables->addColumn('check', function ($index) {
            $html = '';

            $html .= '
                <input type="checkbox" class="check-payroll" value="' . $index->id . '" name="id[]" form="action">
            ';

            return $html;
        });

        $datatables = $datatables->make(true);
        return $datatables;
    }


    public function editPayroll($id)
    {
        $index = Payroll::find($id);

        return view('backend.employee.payroll.edit')->with(compact('index'));
    }

    public function updatePayroll($id, Request $request)
    {
        $this->validate($request, [
            'date_change' => 'required|date',
            'gaji_pokok' => 'required|numeric',
            'tunjangan' => 'required|numeric',
            'perawatan_motor' => 'required|numeric',
            'uang_makan' => 'required|numeric',
            'transport' => 'required|numeric',

            'bpjs_kesehatan' => 'required|numeric',
            'bpjs_ketenagakerjaan' => 'required|numeric',
            'pph' => 'required|numeric',

            'note' => 'required',
            'uang_telat' => 'required|numeric',
            'uang_lembur' => 'required|numeric',
            'update_payroll' => 'required|date',
        ]);

        $index = Payroll::find($id);

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
        $index->update_payroll       = date('Y-m-d', strtotime($request->update_payroll));
        $index->note                 = $request->note;

        $index->save();

        return redirect()->route('admin.employee.edit', ['id' => $index->id_employee])->with('success', 'Data berhasil diupdate');
    }

    public function deletePayroll(Request $request)
    {
        Payroll::destroy($request->id);

        Session::flash('success', 'Data berhasil dihapus');
        return redirect::back();
    }

    public function actionPayroll(Request $request)
    {
        if ($request->action == 'delete') {
            Payroll::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
        } else if ($request->action == 'enable') {
            $index = Payroll::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
        } else if ($request->action == 'disable') {
            $index = Payroll::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
        }

        return redirect::back();
    }
}
