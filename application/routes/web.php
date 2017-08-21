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

// Admin User
Route::get('/user', 'Backend\UserController@index')->name('admin.user');
Route::get('/user/create', 'Backend\UserController@create')->name('admin.user.create');
Route::post('/user/create', 'Backend\UserController@store')->name('admin.user.store');
Route::get('/user/{id}/update', 'Backend\UserController@edit')->name('admin.user.edit');
Route::post('/user/{id}/update', 'Backend\UserController@update')->name('admin.user.update');
Route::get('/user/{id}/delete', 'Backend\UserController@delete')->name('admin.user.delete');
Route::post('/user/action', 'Backend\UserController@action')->name('admin.user.action');
Route::get('/user/{id}/status/{action}', 'Backend\UserController@status')->name('admin.user.status');

Route::get('/jobTitle', 'Backend\JobTitleController@index')->name('admin.jobTitle');
Route::get('/jobTitle/create', 'Backend\JobTitleController@create')->name('admin.jobTitle.create');
Route::post('/jobTitle/create', 'Backend\JobTitleController@store')->name('admin.jobTitle.store');
Route::get('/jobTitle/{id}/update', 'Backend\JobTitleController@edit')->name('admin.jobTitle.edit');
Route::post('/jobTitle/{id}/update', 'Backend\JobTitleController@update')->name('admin.jobTitle.update');
Route::get('/jobTitle/{id}/delete', 'Backend\JobTitleController@delete')->name('admin.jobTitle.delete');
Route::post('/jobTitle/action', 'Backend\JobTitleController@action')->name('admin.jobTitle.action');

Route::get('/employee', 'Backend\EmployeeController@index')->name('admin.employee');
Route::get('/employee/create', 'Backend\EmployeeController@create')->name('admin.employee.create');
Route::post('/employee/create', 'Backend\EmployeeController@store')->name('admin.employee.store');
Route::get('/employee/{id}/update', 'Backend\EmployeeController@edit')->name('admin.employee.edit');
Route::post('/employee/{id}/{type}/update', 'Backend\EmployeeController@update')->name('admin.employee.update');
Route::get('/employee/{id}/delete', 'Backend\EmployeeController@delete')->name('admin.employee.delete');
Route::post('/employee/action', 'Backend\EmployeeController@action')->name('admin.employee.action');

Route::get('/employeeContract', 'Backend\EmployeeController@contract')->name('admin.employeeContract');
Route::get('/employeeContract/delete/{id}', 'Backend\EmployeeController@deleteContract')->name('admin.employeeContract.delete');
Route::post('/employeeContract/action', 'Backend\EmployeeController@actionContract')->name('admin.employeeContract.action');

Route::get('/employeePayroll', 'Backend\EmployeeController@payroll')->name('admin.employeePayroll');
Route::get('/employeePayroll/delete/{id}', 'Backend\EmployeeController@deletePayroll')->name('admin.employeePayroll.delete');
Route::post('/employeePayroll/action', 'Backend\EmployeeController@actionPayroll')->name('admin.employeePayroll.action');

Route::get('/employeeFamily/{id}/create', 'Backend\EmployeeController@createFamily')->name('admin.employeeFamily.create');
Route::post('/employeeFamily/{id}/create', 'Backend\EmployeeController@storeFamily')->name('admin.employeeFamily.store');
Route::get('/employeeFamily/{id}/update', 'Backend\EmployeeController@editFamily')->name('admin.employeeFamily.edit');
Route::post('/employeeFamily/{id}/update', 'Backend\EmployeeController@updateFamily')->name('admin.employeeFamily.update');
Route::get('/employeeFamily/{id}/delete', 'Backend\EmployeeController@deleteFamily')->name('admin.employeeFamily.delete');
Route::post('/employeeFamily/action', 'Backend\EmployeeController@actionFamily')->name('admin.employeeFamily.action');

Route::get('/dayoff', 'Backend\DayoffController@index')->name('admin.dayoff');
Route::get('/dayoff/create', 'Backend\DayoffController@create')->name('admin.dayoff.create');
Route::post('/dayoff/create', 'Backend\DayoffController@store')->name('admin.dayoff.store');
Route::get('/dayoff/{id}/update', 'Backend\DayoffController@edit')->name('admin.dayoff.edit');
Route::post('/dayoff/{id}/update', 'Backend\DayoffController@update')->name('admin.dayoff.update');
Route::get('/dayoff/{id}/delete', 'Backend\DayoffController@delete')->name('admin.dayoff.delete');
Route::post('/dayoff/action', 'Backend\DayoffController@action')->name('admin.dayoff.action');

Route::get('/holiday', 'Backend\HolidayController@index')->name('admin.holiday');
Route::get('/holiday/create', 'Backend\HolidayController@create')->name('admin.holiday.create');
Route::post('/holiday/create', 'Backend\HolidayController@store')->name('admin.holiday.store');
Route::get('/holiday/{id}/update', 'Backend\HolidayController@edit')->name('admin.holiday.edit');
Route::post('/holiday/{id}/update', 'Backend\HolidayController@update')->name('admin.holiday.update');
Route::get('/holiday/{id}/delete', 'Backend\HolidayController@delete')->name('admin.holiday.delete');
Route::post('/holiday/action', 'Backend\HolidayController@action')->name('admin.holiday.action');

Route::get('/calender', 'Backend\CalenderController@index')->name('admin.calender');

Route::get('/itemBorrowed', 'Backend\ItemBorrowedController@index')->name('admin.itemBorrowed');
Route::get('/itemBorrowed/create', 'Backend\ItemBorrowedController@create')->name('admin.itemBorrowed.create');
Route::post('/itemBorrowed/create', 'Backend\ItemBorrowedController@store')->name('admin.itemBorrowed.store');
Route::get('/itemBorrowed/{id}/update', 'Backend\ItemBorrowedController@edit')->name('admin.itemBorrowed.edit');
Route::post('/itemBorrowed/{id}/update', 'Backend\ItemBorrowedController@update')->name('admin.itemBorrowed.update');
Route::get('/itemBorrowed/{id}/delete', 'Backend\ItemBorrowedController@delete')->name('admin.itemBorrowed.delete');
Route::post('/itemBorrowed/action', 'Backend\ItemBorrowedController@action')->name('admin.itemBorrowed.action');

Route::get('/leave', 'Backend\LeaveController@index')->name('admin.leave');
Route::get('/leave/create', 'Backend\LeaveController@create')->name('admin.leave.create');
Route::post('/leave/create', 'Backend\LeaveController@store')->name('admin.leave.store');
Route::get('/leave/{id}/update', 'Backend\LeaveController@edit')->name('admin.leave.edit');
Route::post('/leave/{id}/update', 'Backend\LeaveController@update')->name('admin.leave.update');
Route::get('/leave/{id}/delete', 'Backend\LeaveController@delete')->name('admin.leave.delete');
Route::post('/leave/action', 'Backend\LeaveController@action')->name('admin.leave.action');

Route::get('/shift', 'Backend\ShiftController@index')->name('admin.shift');
Route::get('/shift/create', 'Backend\ShiftController@create')->name('admin.shift.create');
Route::post('/shift/create', 'Backend\ShiftController@store')->name('admin.shift.store');
Route::get('/shift/{id}/update', 'Backend\ShiftController@edit')->name('admin.shift.edit');
Route::post('/shift/{id}/update', 'Backend\ShiftController@update')->name('admin.shift.update');
Route::get('/shift/{id}/delete', 'Backend\ShiftController@delete')->name('admin.shift.delete');
Route::post('/shift/action', 'Backend\ShiftController@action')->name('admin.shift.action');

Route::get('/shiftDetail/{id}/create', 'Backend\ShiftController@createDetail')->name('admin.shiftDetail.create');
Route::post('/shiftDetail/{id}/create', 'Backend\ShiftController@storeDetail')->name('admin.shiftDetail.store');
Route::get('/shiftDetail/{id}/update', 'Backend\ShiftController@editDetail')->name('admin.shiftDetail.edit');
Route::post('/shiftDetail/{id}/update', 'Backend\ShiftController@updateDetail')->name('admin.shiftDetail.update');
Route::get('/shiftDetail/{id}/delete', 'Backend\ShiftController@deleteDetail')->name('admin.shiftDetail.delete');
Route::post('/shiftDetail/action', 'Backend\ShiftController@actionDetail')->name('admin.shiftDetail.action');

Route::get('/attendance', 'Backend\AttendanceController@index')->name('admin.attendance');
Route::get('/attendance/create', 'Backend\AttendanceController@create')->name('admin.attendance.create');
Route::post('/attendance/create', 'Backend\AttendanceController@store')->name('admin.attendance.store');
Route::get('/attendance/{id}/update', 'Backend\AttendanceController@edit')->name('admin.attendance.edit');
Route::post('/attendance/{id}/update', 'Backend\AttendanceController@update')->name('admin.attendance.update');
Route::get('/attendance/{id}/delete', 'Backend\AttendanceController@delete')->name('admin.attendance.delete');
Route::post('/attendance/action', 'Backend\AttendanceController@action')->name('admin.attendance.action');

Route::get('/absence', 'Backend\AbsenceController@index')->name('admin.absence');
Route::get('/absence/create', 'Backend\AbsenceController@create')->name('admin.absence.create');
Route::post('/absence/create', 'Backend\AbsenceController@store')->name('admin.absence.store');
Route::get('/absence/{id}/update', 'Backend\AbsenceController@edit')->name('admin.absence.edit');
Route::post('/absence/{id}/update', 'Backend\AbsenceController@update')->name('admin.absence.update');
Route::get('/absence/{id}/delete', 'Backend\AbsenceController@delete')->name('admin.absence.delete');
Route::post('/absence/action', 'Backend\AbsenceController@action')->name('admin.absence.action');
Route::get('/absence/{id}/employee', 'Backend\AbsenceController@employee')->name('admin.absence.employee');
Route::get('/absence/{id}/employeeDetail', 'Backend\AbsenceController@employeeDetail')->name('admin.absence.employeeDetail');

Route::get('/overtime', 'Backend\OvertimeController@index')->name('admin.overtime');
Route::get('/overtime/create', 'Backend\OvertimeController@create')->name('admin.overtime.create');
Route::post('/overtime/create', 'Backend\OvertimeController@store')->name('admin.overtime.store');
Route::get('/overtime/{id}/update', 'Backend\OvertimeController@edit')->name('admin.overtime.edit');
Route::post('/overtime/{id}/update', 'Backend\OvertimeController@update')->name('admin.overtime.update');
Route::get('/overtime/{id}/delete', 'Backend\OvertimeController@delete')->name('admin.overtime.delete');
Route::post('/overtime/action', 'Backend\OvertimeController@action')->name('admin.overtime.action');