@extends('backend.layout.master')

@section('title')
	Shift - Buat
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
				<label for="work_in_holiday" class="control-label col-md-3 col-sm-3 col-xs-12">Kerja dihari libur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline">
						<input type="checkbox" id="work_in_holiday" name="work_in_holiday" class="{{$errors->first('work_in_holiday') != '' ? 'parsley-error' : ''}}" @if(old('work_in_holiday')) checked @endif>
						Ya
					</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('work_in_holiday') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="day_per_month" class="control-label col-md-3 col-sm-3 col-xs-12">Hari per bulan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="day_per_month" name="day_per_month" class="form-control {{$errors->first('day_per_month') != '' ? 'parsley-error' : ''}}" value="{{ old('day_per_month') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('day_per_month') }}</li>
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