@extends('backend.layout.master')

@section('title')
	Edit Role
@endsection

@section('script')
<script src="{{ asset('backend/js/moment/moment.min.js') }}"></script>
<script src="{{ asset('backend/js/datepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
	$(function() {
		$('input[name=date]').daterangepicker({
		    singleDatePicker: true,
		    showDropdowns: true,
		    format: 'DD MMMM YYYY'
		});

		$(".check-all").click(function(){
			if ($(this).is(':checked'))
			{
				$('.' + $(this).attr('data-target')).prop('checked', true);
			}
			else
			{
				$('.' + $(this).attr('data-target')).prop('checked', false);
			}
		});
	});
</script>

@endsection

@section('content')

	<h1>Edit Role</h1>
	<div class="x_panel">
	<form class="form-horizontal form-label-left" action="{{ route('admin.role.update', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">

		<div class="form-group">
			<label for="name" class="control-label col-md-3 col-sm-3 col-xs-12">Nama Role <span class="required">*</span>
			</label>
			<div class="col-md-9 col-sm-9 col-xs-12">
				<input type="text" id="name" name="name" class="form-control {{$errors->first('name') != '' ? 'parsley-error' : ''}}" value="{{ old('name', $index->name) }}">
				<ul class="parsley-errors-list filled">
					<li class="parsley-required">{{ $errors->first('name') }}</li>
				</ul>
			</div>
		</div>

		<div class="form-group">
			<label for="code" class="control-label col-md-3 col-sm-3 col-xs-12">Kode <span class="required">*</span>
			</label>
			<div class="col-md-9 col-sm-9 col-xs-12">
				<input type="text" id="code" name="code" class="form-control {{$errors->first('code') != '' ? 'parsley-error' : ''}}" value="{{ old('code', $index->code) }}">
				<ul class="parsley-errors-list filled">
					<li class="parsley-required">{{ $errors->first('code') }}</li>
				</ul>
			</div>
		</div>

		<div class="form-group">
			<label for="active" class="control-label col-md-3 col-sm-3 col-xs-12"> 
			</label>
			<div class="col-md-9 col-sm-9 col-xs-12">
				<label class="checkbox-inline"><input type="checkbox" value="1" name="active" @if(old('active', $index->active) == "1") checked @endif>Active</label>
				<ul class="parsley-errors-list filled">
					<li class="parsley-required">{{ $errors->first('active') }}</li>
				</ul>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label checkbox-inline col-md-3 col-sm-3 col-xs-12">
				<input type="checkbox" data-target="group-master" class="check-all"> Config

			</label>
			<div class="col-md-9 col-sm-9 col-xs-12">
				<label class="checkbox-inline"><input type="checkbox" name="permission[]" class="group-master" value="config" @if(in_array('config', old('permission', explode(', ', $index->permission)))) checked @endif>Configuration</label>
				<ul class="parsley-errors-list filled">
					<li class="parsley-required">{{ $errors->first('permission') }}</li>
				</ul>
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label checkbox-inline col-md-3 col-sm-3 col-xs-12">
				<input type="checkbox" data-target="group-developer" class="check-all"> Developer

			</label>
			<div class="col-md-9 col-sm-9 col-xs-12">
				<label class="checkbox-inline"><input type="checkbox" name="permission[]" class="group-developer" value="migration" @if(in_array('migration', old('permission', explode(', ', $index->permission)))) checked @endif>Migration Table</label>
				<label class="checkbox-inline"><input type="checkbox" name="permission[]" class="group-developer" value="sql" @if(in_array('sql', old('permission', explode(', ', $index->permission)))) checked @endif>SQL</label>
				<ul class="parsley-errors-list filled">
					<li class="parsley-required">{{ $errors->first('permission') }}</li>
				</ul>
			</div>
		</div>

		@foreach($key as $list)
			<div class="form-group">
				<label class="control-label checkbox-inline col-md-3 col-sm-3 col-xs-12">
					<input type="checkbox" data-target="group-{{ $list['id'] }}" class="check-all"> Access {{ $list['name'] }}
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					@foreach($list['data'] as $list2)
					<label class="checkbox-inline"><input type="checkbox" name="permission[]" class="group-{{ $list['id'] }}" value="{{ $list2['value'] }}" @if(in_array($list2['value'], old('permission', explode(', ', $index->permission)))) checked @endif>{{ $list2['name'] }}</label>
					@endforeach
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('permission') }}</li>
					</ul>
				</div>
			</div>
		@endforeach

		<div class="form-group">
			<label for="password" class="control-label col-md-3 col-sm-3 col-xs-12">Password User<span class="required">*</span>
			</label>
			<div class="col-md-9 col-sm-9 col-xs-12">
				<input type="password" id="password" name="password" class="form-control {{$errors->first('password') != '' ? 'parsley-error' : ''}}">
				<ul class="parsley-errors-list filled">
					<li class="parsley-required">{{ $errors->first('password') }}</li>
				</ul>
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group">
			<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
				{{ csrf_field() }}
				<a class="btn btn-primary" href="{{ route('admin.role') }}">Batal</a>
				<button type="submit" class="btn btn-success">Submit</button>
			</div>
		</div>

	</form>
	</div>

@endsection