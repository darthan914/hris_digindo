@extends('backend.layout.master')

@section('title')
	Shift {{ $shift->name }} - Edit
@endsection

@section('script')
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

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

		$('#datatable-buttons').DataTable({
			"columnDefs": [
			    { "orderable": false, "targets": 0 }
			]
		});

	});
</script>
@endsection

@section('content')

	<h1>Shift {{ $shift->name }} - Edit</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.shift.updateDetail', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">


			<div class="form-group">
				<label for="shift_in" class="control-label col-md-3 col-sm-3 col-xs-12">Jam Masuk <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="shift_in" name="shift_in" class="form-control {{$errors->first('shift_in') != '' ? 'parsley-error' : ''}}" value="{{ old('shift_in') == '' ? $index->shift_in : old('shift_in') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('shift_in') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="shift_out" class="control-label col-md-3 col-sm-3 col-xs-12">Jam Keluar <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="shift_out" name="shift_out" class="form-control {{$errors->first('shift_out') != '' ? 'parsley-error' : ''}}" value="{{ old('shift_out') == '' ? $index->shift_out : old('shift_out') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('shift_out') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="day" class="control-label col-md-3 col-sm-3 col-xs-12">Untuk Hari
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					
					<label class="radio-inline">
						<input type="radio" name="day" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="0" @if(old('day') != '' && old('day') == 0) checked @elseif($index->day == 0) checked @endif>
						Minggu
					</label>
					<label class="radio-inline">
						<input type="radio" name="day" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="1" @if(old('day') != '' && old('day') == 1) checked @elseif($index->day == 1) checked @endif>
						Senin
					</label>
					<label class="radio-inline">
						<input type="radio" name="day" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="2" @if(old('day') != '' && old('day') == 2) checked @elseif($index->day == 2) checked @endif>
						Selasa
					</label>
					<label class="radio-inline">
						<input type="radio" name="day" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="3" @if(old('day') != '' && old('day') == 3) checked @elseif($index->day == 3) checked @endif>
						Rabu
					</label>
					<label class="radio-inline">
						<input type="radio" name="day" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="4" @if(old('day') != '' && old('day') == 4) checked @elseif($index->day == 4) checked @endif>
						Kamis
					</label>
					<label class="radio-inline">
						<input type="radio" name="day" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="5" @if(old('day') != '' && old('day') == 5) checked @elseif($index->day == 5) checked @endif>
						Jumat
					</label>
					<label class="radio-inline">
						<input type="radio" name="day" class="{{$errors->first('day') != '' ? 'parsley-error' : ''}}" value="6" @if(old('day') != '' && old('day') == 6) checked @elseif($index->day == 6) checked @endif>
						Sabtu
					</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('day') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.shift.edit', ['id' => $shift->id]) }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>

@endsection