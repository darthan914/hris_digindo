@extends('backend.layout.master')

@section('title')
	Shift - Buat
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/timepicker/jquery.timepicker.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/timepicker/jquery.timepicker.min.css') }}" />

<script>
	$(function() {
	    $('input[name="shift_in"], input[name="shift_out"]').timepicker({
		    timeFormat: 'H:mm',
		    interval: 30,
		    startTime: '5:00',
		    dynamic: false,
		    dropdown: true,
		    scrollbar: true
		});

	});
</script>
@endsection

@section('content')

	<h1>Shift - Buat</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.shift.store') }}" method="post" enctype="multipart/form-data">

			
			<div class="form-group">
				<label for="name" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="name" name="name" class="form-control {{$errors->first('name') != '' ? 'parsley-error' : ''}}" value="{{ old('name') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('name') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="code" class="control-label col-md-3 col-sm-3 col-xs-12">Kode <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="code" name="code" class="form-control {{$errors->first('code') != '' ? 'parsley-error' : ''}}" value="{{ old('code') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('code') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="shift_in" class="control-label col-md-3 col-sm-3 col-xs-12">Jam Masuk <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="shift_in" name="shift_in" class="form-control {{$errors->first('shift_in') != '' ? 'parsley-error' : ''}}" value="{{ old('shift_in') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('shift_in') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="shift_out" class="control-label col-md-3 col-sm-3 col-xs-12">Jam Keluar <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="shift_out" name="shift_out" class="form-control {{$errors->first('shift_out') != '' ? 'parsley-error' : ''}}" value="{{ old('shift_out') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('shift_out') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="day" class="control-label col-md-3 col-sm-3 col-xs-12">Untuk Hari
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					
					<label class="checkbox-inline">
						<input type="checkbox" name="day[]" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="0" @if(is_array(old('day')) && in_array(0, old('day'))) checked @endif>
						Minggu
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" name="day[]" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="1" @if(is_array(old('day')) && in_array(1, old('day'))) checked @endif>
						Senin
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" name="day[]" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="2" @if(is_array(old('day')) && in_array(2, old('day'))) checked @endif>
						Selasa
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" name="day[]" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="3" @if(is_array(old('day')) && in_array(3, old('day'))) checked @endif>
						Rabu
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" name="day[]" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="4" @if(is_array(old('day')) && in_array(4, old('day'))) checked @endif>
						Kamis
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" name="day[]" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="5" @if(is_array(old('day')) && in_array(5, old('day'))) checked @endif>
						Jumat
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" name="day[]" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="6" @if(is_array(old('day')) && in_array(6, old('day'))) checked @endif>
						Sabtu
					</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('day') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="work_in_holiday" class="control-label col-md-3 col-sm-3 col-xs-12">Kerja dihari libur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline">
						<input type="checkbox" id="work_in_holiday" name="work_in_holiday" class="{{$errors->first('work_in_holiday') != '' ? 'parsley-error' : ''}}" @if(old('work_in_holiday')) checked @endif>
						Libur dihari kerja
					</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('work_in_holiday') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="late" class="control-label col-md-3 col-sm-3 col-xs-12">Kelipatan Telat <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="late" name="late" class="form-control {{$errors->first('late') != '' ? 'parsley-error' : ''}}" value="{{ old('late') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('late') }}</li>
					</ul>
				</div>
			</div>

			


			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.shift') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection