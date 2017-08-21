<?php

namespace App\Http\Controllers\Backend;

use App\BookContract;
use App\BookPayrollChange;
use App\Employee;
use App\EmployeeFamily;
use App\Http\Controllers\Controller;
use App\JobTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Session;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $index  = Employee::all();
        $active = Employee::whereDate('date_resign', '>', date('Y-m-d'))->orwhereNull('date_resign')->get();
        $resign = Employee::whereNotNull('date_resign')->get();

        return view('backend.employee.index')->with(compact('index', 'active', 'resign'));
    }

    public function create()
    {
        $jobTitle = JobTitle::all();
        $headEmployee = Employee::where('level', 'leader')->whereNull('date_resign')->get();

        return view('backend.employee.create')->with(compact('jobTitle', 'headEmployee'));
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name'                 => 'required',
            'birthday'             => 'required|date',
            'gender'               => 'required',
            'region'               => 'required',
            'status'               => 'required',
            'ktp_address'          => 'required',
            'no_ktp'               => 'required',
            'nik'                  => 'required|unique:employee,nik',
            'id_job_title'         => 'required',
            'date_join'            => 'required|date',
            'phone'                => 'required',

            'type_contract'        => 'required',
            'date_contract'        => 'required|date',
            'end_contract'         => 'required|date',

            'gaji_pokok'           => 'required|numeric',
            'tunjangan'            => 'numeric|nullable',
            'perawatan_motor'      => 'numeric|nullable',
            'uang_makan'           => 'numeric|nullable',
            'transport'            => 'numeric|nullable',
            'bpjs_kesehatan'       => 'numeric|nullable',
            'bpjs_ketenagakerjaan' => 'numeric|nullable',
            'pph'                  => 'numeric|nullable',
            'update_payroll'       => 'required|date',

            'level'                => 'required',
            'leader'               => 'required|integer',
            'npwp'                 => 'nullable',
            'uang_telat'           => 'required|numeric',
            'uang_lembur'          => 'required|numeric',
            'id_machine'           => 'nullable|integer',
        ]);

        $index = new Employee;

        $index->name                 = $request->name;
        $index->birthday             = date('Y-m-d', strtotime($request->birthday));
        $index->gender               = $request->gender;
        $index->region               = $request->region;
        $index->status               = $request->status;
        $index->ktp_address          = $request->ktp_address;
        $index->current_address      = $request->current_address;
        $index->no_ktp               = $request->no_ktp;
        $index->phone                = $request->phone;
        $index->npwp                 = $request->npwp;
        
        $index->nik                  = $request->nik;
        $index->id_job_title         = $request->id_job_title;
        $index->division             = $request->division;
        $index->sub_division         = $request->sub_division;
        $index->level                = $request->level;
        $index->leader               = $request->leader;
        $index->date_join            = date('Y-m-d', strtotime($request->date_join));
        $index->id_machine           = $request->id_machine ? $request->id_machine : 0;

        $index->test_disc            = $request->test_disc;
        $index->test_gratyo          = $request->test_gratyo;
        $index->test_math            = $request->test_math;

        $index->emergency_name       = $request->emergency_name;
        $index->emergency_phone      = $request->emergency_phone;
        $index->emergency_relation   = $request->emergency_relation;

        $index->type_contract        = $request->type_contract;
        $index->date_contract        = date('Y-m-d', strtotime($request->date_contract));
        $index->end_contract         = date('Y-m-d', strtotime($request->end_contract));
        $index->guarantee            = $request->guarantee;
        $index->ref                  = $request->ref;

        $index->gaji_pokok           = $request->gaji_pokok;
        $index->tunjangan            = $request->tunjangan;
        $index->perawatan_motor      = $request->perawatan_motor;
        $index->uang_makan           = $request->uang_makan;
        $index->transport            = $request->transport;
        $index->bpjs_kesehatan       = $request->bpjs_kesehatan;
        $index->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
        $index->pph                  = $request->pph;
        $index->uang_telat           = $request->uang_telat;
        $index->uang_lembur          = $request->uang_lembur;
        $index->update_payroll       = date('Y-m-d', strtotime($request->update_payroll));

        $index->save();

        $contract = new BookContract;

        $contract->id_employee   = $index->id;
        $contract->date_change   = date('Y-m-d');
        $contract->type_contract = $request->type_contract;
        $contract->date_contract = date('Y-m-d', strtotime($request->date_contract));
        $contract->end_contract  = date('Y-m-d', strtotime($request->end_contract));
        $contract->note          = 'pertama';

        $contract->save();

        $payroll = new BookPayrollChange;

        $payroll->id_employee          = $index->id;
        $payroll->date_change          = date('Y-m-d');
        $payroll->gaji_pokok           = $request->gaji_pokok;
        $payroll->tunjangan            = $request->tunjangan;
        $payroll->perawatan_motor      = $request->perawatan_motor;
        $payroll->uang_makan           = $request->uang_makan;
        $payroll->transport            = $request->transport;
        $payroll->bpjs_kesehatan       = $request->bpjs_kesehatan;
        $payroll->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
        $payroll->pph                  = $request->pph;
        $payroll->uang_telat           = $request->uang_telat;
        $payroll->uang_lembur          = $request->uang_lembur;
        $payroll->update_payroll       = date('Y-m-d', strtotime($request->update_payroll));
        $payroll->note                 = 'pertama';

        $payroll->save();

        Session::flash('success', 'Data Has Been Added');
        return redirect()->route('admin.employee.edit', ['id' => $index->id]);
    }

    public function edit($id)
    {
        $index    = Employee::find($id);
        $family   = EmployeeFamily::where('id_employee', $id)->get();
        $jobTitle = JobTitle::all();
        $headEmployee = Employee::where('level', 'leader')->whereNull('date_resign')->get();

        return view('backend.employee.edit')->with(compact('index', 'family', 'jobTitle', 'headEmployee'));
    }

    public function update($id, $type, Request $request)
    {
        $index = Employee::find($id);

        if ($type == 'biodata') {
            $this->validate($request, [
                'name'        => 'required',
                'birthday'    => 'required|date',
                'gender'      => 'required',
                'region'      => 'required',
                'status'      => 'required',
                'ktp_address' => 'required',
                'no_ktp'      => 'required',
                'phone'       => 'required',
            ]);

            $index->name            = $request->name;
            $index->birthday        = date('Y-m-d', strtotime($request->birthday));
            $index->gender          = $request->gender;
            $index->region          = $request->region;
            $index->status          = $request->status;
            $index->ktp_address     = $request->ktp_address;
            $index->current_address = $request->current_address;
            $index->no_ktp          = $request->no_ktp;
            $index->npwp            = $request->npwp;
            $index->phone           = $request->phone;

        } else if ($type == 'data') {
            $this->validate($request, [
                'nik'          => 'required|unique:employee,nik,' . $id,
                'id_job_title' => 'required',
                'date_join'    => 'required|date',
                'level'        => 'required',
                'leader'       => 'required|integer',
                'id_machine'   => 'nullable|integer',
            ]);

            $index->nik          = $request->nik;
            $index->id_job_title = $request->id_job_title;
            $index->division     = $request->division;
            $index->sub_division = $request->sub_division;
            $index->level        = $request->level;
            $index->leader       = $request->leader;
            $index->id_machine   = $request->id_machine;
            $index->date_join    = date('Y-m-d', strtotime($request->date_join));

        } else if ($type == 'contract') {
            $this->validate($request, [
                'type_contract' => 'required',
                'date_contract' => 'required|date',
                'end_contract'  => 'required|date',
                'note_contract' => 'required',
            ]);

            $contract = new BookContract;

            $contract->id_employee   = $id;
	        $contract->date_change   = date('Y-m-d');
            $contract->type_contract = $request->type_contract;
            $contract->date_contract = date('Y-m-d', strtotime($request->date_contract));
            $contract->end_contract  = date('Y-m-d', strtotime($request->end_contract));
            $contract->note          = $request->note_contract;

            $contract->save();

            $index->type_contract = $request->type_contract;
            $index->date_contract = date('Y-m-d', strtotime($request->date_contract));
            $index->end_contract  = date('Y-m-d', strtotime($request->end_contract));
            $index->guarantee     = $request->guarantee;

        } else if ($type == 'payroll') {
            $this->validate($request, [
                'gaji_pokok'           => 'required|numeric',
                'tunjangan'            => 'numeric|nullable',
                'perawatan_motor'      => 'numeric|nullable',
                'uang_makan'           => 'numeric|nullable',
                'transport'            => 'numeric|nullable',
                'bpjs_kesehatan'       => 'numeric|nullable',
                'bpjs_ketenagakerjaan' => 'numeric|nullable',
                'pph'                  => 'numeric|nullable',
                'uang_telat'           => 'required|numeric',
                'update_payroll'       => 'required|date',
                'note_payroll'         => 'required',
            ]);

            $payroll = new BookPayrollChange;

            $payroll->id_employee          = $id;
            $payroll->date_change          = date('Y-m-d');
            $payroll->gaji_pokok           = $request->gaji_pokok;
            $payroll->tunjangan            = $request->tunjangan;
            $payroll->perawatan_motor      = $request->perawatan_motor;
            $payroll->uang_makan           = $request->uang_makan;
            $payroll->transport            = $request->transport;
            $payroll->bpjs_kesehatan       = $request->bpjs_kesehatan;
            $payroll->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
            $payroll->pph                  = $request->pph;
            $payroll->uang_telat           = $request->uang_telat;
            $payroll->uang_lembur          = $request->uang_lembur;
            $payroll->update_payroll       = date('Y-m-d', strtotime($request->update_payroll));
            $payroll->note                 = $request->note_payroll;

            $payroll->save();

            $index->gaji_pokok           = $request->gaji_pokok;
            $index->tunjangan            = $request->tunjangan;
            $index->perawatan_motor      = $request->perawatan_motor;
            $index->uang_makan           = $request->uang_makan;
            $index->transport            = $request->transport;
            $index->bpjs_kesehatan       = $request->bpjs_kesehatan;
            $index->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
            $index->pph                  = $request->pph;
            $index->uang_telat           = $request->uang_telat;
            $index->uang_lembur          = $request->uang_lembur;
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

            if($index->date_resign)
            {
                $index->date_resign = NULL;
            }
            else
            {
                $this->validate($request, [
                    'date_resign' => 'required|date',
                ]);
                $index->date_resign = date('Y-m-d', strtotime($request->date_resign));
            }
        }

        $index->save();

        Session::flash('success', 'Data Has Been Updated');
        return redirect::back();
    }

    public function delete($id)
    {
        Employee::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
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

        Session::flash('success', 'Data Has Been Added');
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

        Session::flash('success', 'Data Has Been Updated');
        return redirect()->route('admin.employee.edit', ['id' => $index->id_employee]);
    }

    public function deleteFamily($id)
    {
        EmployeeFamily::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
        return redirect::back();
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

    public function contract(Request $request)
    {
        $f_id_employee = 0;
        if(isset($request->f_id_employee))
        {
            $f_id_employee = $request->f_id_employee;
        }
        $employee = Employee::all();


    	$index = BookContract::orderBy('id', 'ASC');

        if($f_id_employee != 0)
        {
            $index = $index->where('id_employee', $f_id_employee);
        }
        
        $index = $index->get();

    	return view('backend.employee.contract')->with(compact('index', 'employee', 'f_id_employee'));
    }

    public function deleteContract($id)
    {
        BookContract::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
        return redirect::back();
    }

    public function actionContract(Request $request)
    {
        if ($request->action == 'delete') {
            BookContract::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
        } else if ($request->action == 'enable') {
            $index = BookContract::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
        } else if ($request->action == 'disable') {
            $index = BookContract::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
        }

        return redirect::back();
    }


    public function payroll(Request $request)
    {
        $f_id_employee = 0;
        if(isset($request->f_id_employee))
        {
            $f_id_employee = $request->f_id_employee;
        }
        $employee = Employee::all();

    	$index = BookPayrollChange::orderBy('id', 'ASC');

        if($f_id_employee != 0)
        {
            $index = $index->where('id_employee', $f_id_employee);
        }

    	$index = $index->get();
    	
    	return view('backend.employee.payroll')->with(compact('index', 'employee', 'f_id_employee'));
    }

    public function deletePayroll($id)
    {
        BookPayrollChange::destroy($id);

        Session::flash('success', 'Data Has Been Deleted');
        return redirect::back();
    }

    public function actionPayroll(Request $request)
    {
        if ($request->action == 'delete') {
            BookPayrollChange::destroy($request->id);
            Session::flash('success', 'Data Selected Has Been Deleted');
        } else if ($request->action == 'enable') {
            $index = BookPayrollChange::whereIn('id', $request->id)->update(['active' => 1]);
            Session::flash('success', 'Data Selected Has Been Actived');
        } else if ($request->action == 'disable') {
            $index = BookPayrollChange::whereIn('id', $request->id)->update(['active' => 0]);
            Session::flash('success', 'Data Selected Has Been Inactived');
        }

        return redirect::back();
    }
}
