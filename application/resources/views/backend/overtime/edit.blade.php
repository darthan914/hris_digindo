@extends('backend.layout.master')

@section('title')
	Lembur - Edit
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('backend/vendors/timepicker/jquery.timepicker.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/timepicker/jquery.timepicker.min.css') }}" />

<script type="text/javascript" src="{{ asset('backend/vendors/ckeditor/ckeditor.js') }}"></script>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    // CKEDITOR.replace( 'description' );
    // CKEDITOR.replace( 'short_description' );

    $(function() {
	    $('select[name="id_employee"]').select2();

	    $('input[name="date"]').daterangepicker({
	    	locale: {
		      format: 'DD MMMM YYYY'
		    },
		    singleDatePicker: true,
	        showDropdowns: true
	    });

	    $('input[name="end_overtime"]').daterangepicker({
	    	locale: {
		      format: 'DD MMMM YYYY HH:mm'
		    },
		    singleDatePicker: true,
	        showDropdowns: true,
	        timePicker: true,
		    timePicker24Hour: true,
		});

	});

</script>
@endsection

@section('content')

	<h1>Lembur - Edit</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.overtime.update', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="id_employee" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="id_employee" name="id_employee" class="form-control {{$errors->first('id_employee') != '' ? 'parsley-error' : ''}}">
						<option value="">-- Pilih Karyawan --</option>
						@foreach($employee as $list)
						<option value="{{ $list->id }}" @if(old('id_employee') != '' && old('id_employee') == $list->id) selected @elseif($index->id_employee == $list->id ) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('id_employee') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="date" name="date" class="form-control {{$errors->first('date') != '' ? 'parsley-error' : ''}}" value="{{ old('date') == '' ? date('d F Y', strtotime($index->date)) : old('date') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('date') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="end_overtime" class="control-label col-md-3 col-sm-3 col-xs-12">Lembur <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="end_overtime" name="end_overtime" class="form-control {{$errors->first('end_overtime') != '' ? 'parsley-error' : ''}}" value="{{ old('end_overtime') == '' ? date('d F Y H:i', strtotime($index->end_overtime)) : old('end_overtime') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('end_overtime') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="note" class="control-label col-md-3 col-sm-3 col-xs-12">Catatan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea id="note" name="note" class="form-control {{$errors->first('note') != '' ? 'parsley-error' : ''}}">{{ old('note') == '' ? $index->note : old('note') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('note') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.overtime') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection