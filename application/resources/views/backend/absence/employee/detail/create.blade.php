@extends('backend.layout.master')

@section('title')
	Absen Check Karyawan - Buat
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('backend/vendors/timepicker/jquery.timepicker.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/timepicker/jquery.timepicker.min.css') }}" />

<script type="text/javascript">
	$(function() {
	    $('select[name="id_employee"]').select2();

	    $('input[name="date"]').daterangepicker({
	    	locale: {
		      format: 'DD MMMM YYYY'
		    },
	        singleDatePicker: true,
	        showDropdowns: true,
	    });

	    $('input[name="shift_in"], input[name="shift_out"], input[name="check_in"], input[name="check_out"]').timepicker({
		    timeFormat: 'H:mm',
		    interval: 30,
		    startTime: '5:00',
		    dynamic: false,
		    dropdown: true,
		    scrollbar: true
		});

		$(".btn-pulldata").click(function(){
	    	if($('select[name=date]').val() == '')
	    	{
	    		alert('Masukan tanggal!');
	    	}
	    	else
	    	{
	    		$.post("{{ route('admin.absence.ajaxShift') }}",
		        {
		            id_employee: {{ $index->id_employee }},
		            date: $('input[name=date]').val(),
		        },
		        function(index){
		            $('input[name=shift_in]').val(index.shift_in);
		            $('input[name=shift_out]').val(index.shift_out);
		        });
	    	}
	        	
	    });

	});
</script>
@endsection

@section('content')

	<h1>Absen Check Karyawan - Buat</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.absence.storeEmployeeDetail', [$index->id]) }}" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="date" name="date" class="form-control {{$errors->first('date') != '' ? 'parsley-error' : ''}}" value="{{ old('date') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('date') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					<button type="button" class="btn btn-success btn-pulldata">Pull Data</button>
				</div>
			</div>

			<div class="form-group">
				<label for="shift_in" class="control-label col-md-3 col-sm-3 col-xs-12">Shift In
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="shift_in" name="shift_in" class="form-control {{$errors->first('shift_in') != '' ? 'parsley-error' : ''}}" value="{{ old('shift_in') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('shift_in') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="shift_out" class="control-label col-md-3 col-sm-3 col-xs-12">Shift Out
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="shift_out" name="shift_out" class="form-control {{$errors->first('shift_out') != '' ? 'parsley-error' : ''}}" value="{{ old('shift_out') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('shift_out') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="check_in" class="control-label col-md-3 col-sm-3 col-xs-12">Check In
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="check_in" name="check_in" class="form-control {{$errors->first('check_in') != '' ? 'parsley-error' : ''}}" value="{{ old('check_in') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('check_in') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="check_out" class="control-label col-md-3 col-sm-3 col-xs-12">Check Out
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="check_out" name="check_out" class="form-control {{$errors->first('check_out') != '' ? 'parsley-error' : ''}}" value="{{ old('check_out') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('check_out') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="fine_additional" class="control-label col-md-3 col-sm-3 col-xs-12">Denda
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="fine_additional" name="fine_additional" class="form-control {{$errors->first('fine_additional') != '' ? 'parsley-error' : ''}}" value="{{ old('fine_additional') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('fine_additional') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.absence.editEmployee', [$index->id]) }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection