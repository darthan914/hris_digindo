<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Backend\AuthController@showLogin')->name('admin');
Route::post('/login', 'Backend\AuthController@login')->name('admin.login');
Route::get('/logout', 'Backend\AuthController@logout')->name('admin.logout');
Route::get('/register', 'Backend\AuthController@formRegister')->name('admin.register');
Route::post('/register', 'Backend\AuthController@register')->name('admin.register.save');

Route::get('/home', 'Backend\DashboardController@index')->name('admin.home');
Route::post('/home/ajaxEmployee', 'Backend\DashboardController@ajaxEmployee')->name('admin.home.ajaxEmployee');

// Admin User
Route::get('/user', 'Backend\UserController@index')->name('admin.user')->middleware('can:list-user');
Route::get('/user/index', 'Backend\UserController@index')->name('admin.user.index')->middleware('can:list-user');
Route::post('/user/datatables', 'Backend\UserController@datatables')->name('admin.user.datatables')->middleware('can:list-user');

Route::get('/user/create', 'Backend\UserController@create')->name('admin.user.create')->middleware('can:create-user');
Route::post('/user/store', 'Backend\UserController@store')->name('admin.user.store')->middleware('can:create-user');
Route::get('/user/{id}/edit', 'Backend\UserController@edit')->name('admin.user.edit')->middleware('can:edit-user');
Route::post('/user/{id}/update', 'Backend\UserController@update')->name('admin.user.update')->middleware('can:edit-user');
Route::post('/user/delete', 'Backend\UserController@delete')->name('admin.user.delete')->middleware('can:delete-user');
Route::post('/user/action', 'Backend\UserController@action')->name('admin.user.action');
Route::post('/user/active', 'Backend\UserController@active')->name('admin.user.active')->middleware('can:active-user');
Route::get('/user/{id}/access', 'Backend\UserController@access')->name('admin.user.access')->middleware('can:access-user');
Route::post('/user/{id}/accessUpdate', 'Backend\UserController@accessUpdate')->name('admin.user.accessUpdate')->middleware('can:access-user');
Route::post('/user/impersonate', 'Backend\UserController@impersonate')->name('admin.user.impersonate')->middleware('can:impersonate-user');
Route::post('/user/leave', 'Backend\UserController@accessUpdate')->name('admin.user.leave');


// Admin Position
Route::get('/role', 'Backend\RoleController@index')->name('admin.role')->middleware('can:list-role');
Route::get('/role/index', 'Backend\RoleController@index')->name('admin.role.index')->middleware('can:list-role');
Route::post('/role/datatables', 'Backend\RoleController@datatables')->name('admin.role.datatables')->middleware('can:list-role');

Route::get('/role/create', 'Backend\RoleController@create')->name('admin.role.create')->middleware('can:create-role');
Route::post('/role/store', 'Backend\RoleController@store')->name('admin.role.store')->middleware('can:create-role');
Route::get('/role/{id}/edit', 'Backend\RoleController@edit')->name('admin.role.edit')->middleware('can:edit-role');
Route::post('/role/{id}/update', 'Backend\RoleController@update')->name('admin.role.update')->middleware('can:edit-role');
Route::post('/role/delete', 'Backend\RoleController@delete')->name('admin.role.delete')->middleware('can:delete-role');
Route::post('/role/action', 'Backend\RoleController@action')->name('admin.role.action');
Route::post('/role/active', 'Backend\RoleController@active')->name('admin.role.active')->middleware('can:active-role');


// Admin Employee
Route::get('/employee', 'Backend\EmployeeController@index')->name('admin.employee')->middleware('can:list-employee');
Route::get('/employee/index', 'Backend\EmployeeController@index')->name('admin.employee.index')->middleware('can:list-employee');
Route::post('/employee/datatables', 'Backend\EmployeeController@datatables')->name('admin.employee.datatables')->middleware('can:list-employee');

Route::get('/employee/create', 'Backend\EmployeeController@create')->name('admin.employee.create')->middleware('can:create-employee');
Route::post('/employee/store', 'Backend\EmployeeController@store')->name('admin.employee.store')->middleware('can:create-employee');
Route::get('/employee/{id}/edit', 'Backend\EmployeeController@edit')->name('admin.employee.edit')->middleware('can:view-employee');
Route::post('/employee/{id}/{type}/update', 'Backend\EmployeeController@update')->name('admin.employee.update')->middleware('can:edit-employee');
Route::post('/employee/delete', 'Backend\EmployeeController@delete')->name('admin.employee.delete')->middleware('can:delete-employee');
Route::post('/employee/action', 'Backend\EmployeeController@action')->name('admin.employee.action');


Route::post('/employee/{id}/datatablesFamily', 'Backend\EmployeeController@datatablesFamily')->name('admin.employee.datatablesFamily')->middleware('can:view-employee');
Route::get('/employee/{id}/createFamily', 'Backend\EmployeeController@createFamily')->name('admin.employee.createFamily')->middleware('can:createFamily-employee');
Route::post('/employee/{id}/storeFamily', 'Backend\EmployeeController@storeFamily')->name('admin.employee.storeFamily')->middleware('can:createFamily-employee');
Route::get('/employee/{id}/editFamily', 'Backend\EmployeeController@editFamily')->name('admin.employee.editFamily')->middleware('can:editFamily-employee');
Route::post('/employee/{id}/updateFamily', 'Backend\EmployeeController@updateFamily')->name('admin.employee.updateFamily')->middleware('can:editFamily-employee');
Route::post('/employee/deleteFamily', 'Backend\EmployeeController@deleteFamily')->name('admin.employee.deleteFamily')->middleware('can:deleteFamily-employee');
Route::post('/employee/actionFamily', 'Backend\EmployeeController@actionFamily')->name('admin.employee.actionFamily');


Route::get('/employee/contract', 'Backend\EmployeeController@contract')->name('admin.employee.contract')->middleware('can:view-employee');
Route::post('/employee/{id}/datatablesContract', 'Backend\EmployeeController@datatablesContract')->name('admin.employee.datatablesContract')->middleware('can:view-employee');

Route::get('/employee/{id}/editContract', 'Backend\EmployeeController@editContract')->name('admin.employee.editContract')->middleware('can:editContract-employee');
Route::post('/employee/{id}/updateContract', 'Backend\EmployeeController@updateContract')->name('admin.employee.updateContract')->middleware('can:editContract-employee');
Route::post('/employee/deleteContract', 'Backend\EmployeeController@deleteContract')->name('admin.employee.deleteContract')->middleware('can:deleteContract-employee');
Route::post('/employee/actionContract', 'Backend\EmployeeController@actionContract')->name('admin.employee.actionContract');


Route::get('/employee/payroll', 'Backend\EmployeeController@payroll')->name('admin.employee.payroll')->middleware('can:view-employee');
Route::post('/employee/{id}/datatablesPayroll', 'Backend\EmployeeController@datatablesPayroll')->name('admin.employee.datatablesPayroll')->middleware('can:view-employee');

Route::get('/employee/{id}/editPayroll', 'Backend\EmployeeController@editPayroll')->name('admin.employee.editPayroll')->middleware('can:editPayroll-employee');
Route::post('/employee/{id}/updatePayroll', 'Backend\EmployeeController@updatePayroll')->name('admin.employee.updatePayroll')->middleware('can:editPayroll-employee');
Route::post('/employee/deletePayroll', 'Backend\EmployeeController@deletePayroll')->name('admin.employee.deletePayroll')->middleware('can:deletePayroll-employee');
Route::post('/employee/actionPayroll', 'Backend\EmployeeController@actionPayroll')->name('admin.employee.actionPayroll');


// Admin Dayoff
Route::get('/dayoff', 'Backend\DayoffController@index')->name('admin.dayoff')->middleware('can:list-dayoff');
Route::get('/dayoff/index', 'Backend\DayoffController@index')->name('admin.dayoff.index')->middleware('can:list-dayoff');
Route::post('/dayoff/datatables', 'Backend\DayoffController@datatables')->name('admin.dayoff.datatables')->middleware('can:list-dayoff');
Route::post('/dayoff/datatablesRemain', 'Backend\DayoffController@datatablesRemain')->name('admin.dayoff.datatablesRemain')->middleware('can:list-dayoff');

Route::get('/dayoff/create', 'Backend\DayoffController@create')->name('admin.dayoff.create')->middleware('can:create-dayoff');
Route::post('/dayoff/store', 'Backend\DayoffController@store')->name('admin.dayoff.store')->middleware('can:create-dayoff');
Route::get('/dayoff/{id}/edit', 'Backend\DayoffController@edit')->name('admin.dayoff.edit')->middleware('can:edit-dayoff');
Route::post('/dayoff/{id}/update', 'Backend\DayoffController@update')->name('admin.dayoff.update')->middleware('can:edit-dayoff');
Route::post('/dayoff/delete', 'Backend\DayoffController@delete')->name('admin.dayoff.delete')->middleware('can:delete-dayoff');
Route::post('/dayoff/confirm', 'Backend\DayoffController@confirm')->name('admin.dayoff.confirm')->middleware('can:confirm-dayoff');
Route::post('/dayoff/action', 'Backend\DayoffController@action')->name('admin.dayoff.action');


// Admin Holiday
Route::get('/holiday', 'Backend\HolidayController@index')->name('admin.holiday')->middleware('can:list-holiday');
Route::get('/holiday/index', 'Backend\HolidayController@index')->name('admin.holiday.index')->middleware('can:list-holiday');
Route::post('/holiday/datatables', 'Backend\HolidayController@datatables')->name('admin.holiday.datatables')->middleware('can:list-holiday');

Route::get('/holiday/create', 'Backend\HolidayController@create')->name('admin.holiday.create')->middleware('can:create-holiday');
Route::post('/holiday/store', 'Backend\HolidayController@store')->name('admin.holiday.store')->middleware('can:create-holiday');
Route::get('/holiday/{id}/edit', 'Backend\HolidayController@edit')->name('admin.holiday.edit')->middleware('can:edit-holiday');
Route::post('/holiday/{id}/update', 'Backend\HolidayController@update')->name('admin.holiday.update')->middleware('can:edit-holiday');
Route::post('/holiday/delete', 'Backend\HolidayController@delete')->name('admin.holiday.delete')->middleware('can:delete-holiday');
Route::post('/holiday/action', 'Backend\HolidayController@action')->name('admin.holiday.action');


// Admin Calender
Route::get('/calender', 'Backend\CalenderController@index')->name('admin.calender');
Route::get('/calender/index', 'Backend\CalenderController@index')->name('admin.calender.index');
Route::post('/calender/ajax', 'Backend\CalenderController@ajax')->name('admin.calender.ajax');


// Admin Borrow
Route::get('/borrow', 'Backend\BorrowController@index')->name('admin.borrow')->middleware('can:list-borrow');
Route::get('/borrow/index', 'Backend\BorrowController@index')->name('admin.borrow.index')->middleware('can:list-borrow');
Route::post('/borrow/datatables', 'Backend\BorrowController@datatables')->name('admin.borrow.datatables')->middleware('can:list-borrow');

Route::get('/borrow/create', 'Backend\BorrowController@create')->name('admin.borrow.create')->middleware('can:create-borrow');
Route::post('/borrow/store', 'Backend\BorrowController@store')->name('admin.borrow.store')->middleware('can:create-borrow');
Route::get('/borrow/{id}/edit', 'Backend\BorrowController@edit')->name('admin.borrow.edit')->middleware('can:edit-borrow');
Route::post('/borrow/{id}/update', 'Backend\BorrowController@update')->name('admin.borrow.update')->middleware('can:edit-borrow');
Route::post('/borrow/delete', 'Backend\BorrowController@delete')->name('admin.borrow.delete')->middleware('can:delete-borrow');
Route::post('/borrow/action', 'Backend\BorrowController@action')->name('admin.borrow.action');


// Admin Leave
Route::get('/leave', 'Backend\LeaveController@index')->name('admin.leave')->middleware('can:list-leave');
Route::get('/leave/index', 'Backend\LeaveController@index')->name('admin.leave.index')->middleware('can:list-leave');
Route::post('/leave/datatables', 'Backend\LeaveController@datatables')->name('admin.leave.datatables')->middleware('can:list-leave');

Route::get('/leave/create', 'Backend\LeaveController@create')->name('admin.leave.create')->middleware('can:create-leave');
Route::post('/leave/store', 'Backend\LeaveController@store')->name('admin.leave.store')->middleware('can:create-leave');
Route::get('/leave/{id}/edit', 'Backend\LeaveController@edit')->name('admin.leave.edit')->middleware('can:edit-leave');
Route::post('/leave/{id}/update', 'Backend\LeaveController@update')->name('admin.leave.update')->middleware('can:edit-leave');
Route::post('/leave/delete', 'Backend\LeaveController@delete')->name('admin.leave.delete')->middleware('can:delete-leave');
Route::post('/leave/confirm', 'Backend\LeaveController@confirm')->name('admin.leave.confirm')->middleware('can:confirm-leave');
Route::post('/leave/action', 'Backend\LeaveController@action')->name('admin.leave.action');


// Admin Shift
Route::get('/shift', 'Backend\ShiftController@index')->name('admin.shift')->middleware('can:list-shift');
Route::get('/shift/index', 'Backend\ShiftController@index')->name('admin.shift.index')->middleware('can:list-shift');
Route::post('/shift/datatables', 'Backend\ShiftController@datatables')->name('admin.shift.datatables')->middleware('can:list-shift');

Route::get('/shift/create', 'Backend\ShiftController@create')->name('admin.shift.create')->middleware('can:create-shift');
Route::post('/shift/store', 'Backend\ShiftController@store')->name('admin.shift.store')->middleware('can:create-shift');
Route::get('/shift/{id}/edit', 'Backend\ShiftController@edit')->name('admin.shift.edit')->middleware('can:view-shift');
Route::post('/shift/{id}/update', 'Backend\ShiftController@update')->name('admin.shift.update')->middleware('can:edit-shift');
Route::post('/shift/delete', 'Backend\ShiftController@delete')->name('admin.shift.delete')->middleware('can:delete-shift');
Route::post('/shift/action', 'Backend\ShiftController@action')->name('admin.shift.action');

Route::post('/shift/{id}/datatables', 'Backend\ShiftController@datatablesDetail')->name('admin.shift.datatablesDetail')->middleware('can:view-shift');
Route::get('/shift/{id}/createDetail', 'Backend\ShiftController@createDetail')->name('admin.shift.createDetail')->middleware('can:edit-shift');
Route::post('/shift/{id}/storeDetail', 'Backend\ShiftController@storeDetail')->name('admin.shift.storeDetail')->middleware('can:edit-shift');
Route::get('/shift/{id}/editDetail', 'Backend\ShiftController@editDetail')->name('admin.shift.editDetail')->middleware('can:edit-shift');
Route::post('/shift/{id}/updateDetail', 'Backend\ShiftController@updateDetail')->name('admin.shift.updateDetail')->middleware('can:edit-shift');
Route::post('/shift/deleteDetail', 'Backend\ShiftController@deleteDetail')->name('admin.shift.deleteDetail')->middleware('can:edit-shift');
Route::post('/shift/actionDetail', 'Backend\ShiftController@actionDetail')->name('admin.shift.actionDetail');


// Admin Absence
Route::get('/absence', 'Backend\AbsenceController@index')->name('admin.absence')->middleware('can:list-absence');
Route::get('/absence/index', 'Backend\AbsenceController@index')->name('admin.absence.index')->middleware('can:list-absence');
Route::post('/absence/datatables', 'Backend\AbsenceController@datatables')->name('admin.absence.datatables')->middleware('can:list-absence');

Route::get('/absence/create', 'Backend\AbsenceController@create')->name('admin.absence.create')->middleware('can:create-absence');
Route::post('/absence/store', 'Backend\AbsenceController@store')->name('admin.absence.store')->middleware('can:create-absence');
Route::get('/absence/{id}/edit', 'Backend\AbsenceController@edit')->name('admin.absence.edit')->middleware('can:view-absence');
Route::post('/absence/{id}/update', 'Backend\AbsenceController@update')->name('admin.absence.update')->middleware('can:edit-absence');
Route::post('/absence/delete', 'Backend\AbsenceController@delete')->name('admin.absence.delete')->middleware('can:delete-absence');
Route::post('/absence/action', 'Backend\AbsenceController@action')->name('admin.absence.action');

Route::get('/absence/{id}/employee', 'Backend\AbsenceController@employee')->name('admin.absence.employee')->middleware('can:view-absence');
Route::post('/absence/{id}/datatablesEmployee', 'Backend\AbsenceController@datatablesEmployee')->name('admin.absence.datatablesEmployee')->middleware('can:view-absence');

Route::get('/absence/{id}/createEmployee', 'Backend\AbsenceController@createEmployee')->name('admin.absence.createEmployee')->middleware('can:create-absence');
Route::post('/absence/{id}/storeEmployee', 'Backend\AbsenceController@storeEmployee')->name('admin.absence.storeEmployee')->middleware('can:create-absence');
Route::get('/absence/{id}/editEmployee', 'Backend\AbsenceController@editEmployee')->name('admin.absence.editEmployee')->middleware('can:view-absence');
Route::post('/absence/{id}/updateEmployee', 'Backend\AbsenceController@updateEmployee')->name('admin.absence.updateEmployee')->middleware('can:edit-absence');
Route::post('/absence/deleteEmployee', 'Backend\AbsenceController@deleteEmployee')->name('admin.absence.deleteEmployee')->middleware('can:delete-absence');
Route::post('/absence/actionEmployee', 'Backend\AbsenceController@actionEmployee')->name('admin.absence.actionEmployee');

Route::get('/absence/{id}/employeeDetail', 'Backend\AbsenceController@employeeDetail')->name('admin.absence.employeeDetail')->middleware('can:view-absence');
Route::post('/absence/{id}/datatablesEmployeeDetail', 'Backend\AbsenceController@datatablesEmployeeDetail')->name('admin.absence.datatablesEmployeeDetail')->middleware('can:view-absence');

Route::get('/absence/{id}/createEmployeeDetail', 'Backend\AbsenceController@createEmployeeDetail')->name('admin.absence.createEmployeeDetail')->middleware('can:create-absence');
Route::post('/absence/{id}/storeEmployeeDetail', 'Backend\AbsenceController@storeEmployeeDetail')->name('admin.absence.storeEmployeeDetail')->middleware('can:create-absence');
Route::get('/absence/{id}/editEmployeeDetail', 'Backend\AbsenceController@editEmployeeDetail')->name('admin.absence.editEmployeeDetail')->middleware('can:edit-absence');
Route::post('/absence/{id}/updateEmployeeDetail', 'Backend\AbsenceController@updateEmployeeDetail')->name('admin.absence.updateEmployeeDetail')->middleware('can:edit-absence');
Route::post('/absence/deleteEmployeeDetail', 'Backend\AbsenceController@deleteEmployeeDetail')->name('admin.absence.deleteEmployeeDetail')->middleware('can:delete-absence');
Route::post('/absence/actionEmployeeDetail', 'Backend\AbsenceController@actionEmployeeDetail')->name('admin.absence.actionEmployeeDetail');

Route::post('/absence/ajaxPayroll', 'Backend\AbsenceController@ajaxPayroll')->name('admin.absence.ajaxPayroll');
Route::post('/absence/ajaxShift', 'Backend\AbsenceController@ajaxShift')->name('admin.absence.ajaxShift');


// Admin Overtime
Route::get('/overtime', 'Backend\OvertimeController@index')->name('admin.overtime');
Route::get('/overtime/index', 'Backend\OvertimeController@index')->name('admin.overtime.index');
Route::post('/overtime/datatables', 'Backend\OvertimeController@datatables')->name('admin.overtime.datatables');

Route::get('/overtime/create', 'Backend\OvertimeController@create')->name('admin.overtime.create');
Route::post('/overtime/create', 'Backend\OvertimeController@store')->name('admin.overtime.store');
Route::get('/overtime/{id}/update', 'Backend\OvertimeController@edit')->name('admin.overtime.edit');
Route::post('/overtime/{id}/update', 'Backend\OvertimeController@update')->name('admin.overtime.update');
Route::post('/overtime/delete', 'Backend\OvertimeController@delete')->name('admin.overtime.delete');
Route::post('/overtime/confirm', 'Backend\OvertimeController@confirm')->name('admin.overtime.confirm');
Route::post('/overtime/action', 'Backend\OvertimeController@action')->name('admin.overtime.action');