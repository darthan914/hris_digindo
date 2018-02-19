<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;

use App\Http\Controllers\Controller;

use App\Employee;
use App\User;

class DashboardController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $year = '';
        for ($i=2016; $i <= (int)date('Y') ; $i++) { 
            $year[] = $i; 
        }

        $month = ['Januari', 'Febuari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
    	return view('backend.dashboard.index', compact('request', 'year', 'month'));
    }

    public function ajaxEmployee(Request $request)
    {
        $f_year  = $this->filter($request->f_year, date('Y'));
        $f_month = $this->filter($request->f_month, date('n'));

    	$birthday_today = Employee::whereMonth('birthday', date('n'))->whereDay('birthday', date('j'))->whereNull('date_resign')->orderBy('birthday', 'ASC')->get();
    	$birthday_monthly = Employee::whereMonth('birthday', $f_month)->whereNull('date_resign')->orderBy('birthday', 'ASC')->get();

    	$end_contract_today = Employee::whereDate('end_date_contract', date('Y-m-d'))->orderBy('end_date_contract', 'ASC')->get();
    	$end_contract_monthly = Employee::whereYear('end_date_contract', $f_year)->whereMonth('end_date_contract', $f_month)->orderBy('end_date_contract', 'ASC')->get();

    	return compact('birthday_today', 'birthday_monthly', 'end_contract_today', 'end_contract_monthly');
    }
}
