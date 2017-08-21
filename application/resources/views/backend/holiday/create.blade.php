@extends('backend.layout.master')

@section('title')
	Libur - Buat
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('backend/vendors/ckeditor/ckeditor.js') }}"></script>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    // CKEDITOR.replace( 'description' );
    // CKEDITOR.replace( 'short_description' );

    function autoUrl(from, to)
	{

		temp = document.getElementById(from).value;
		temp = temp.toLowerCase();
		temp = temp.replace(/ /g, "-");
		temp = encodeURI(temp);

		if(temp != '')
		{
			document.getElementById(to).value = temp;
		}
	}

	$(function() {
	    $('input[name="date"]').daterangepicker({
	    	locale: {
		      format: 'DD MMMM YYYY'
		    },
		    singleDatePicker: true,
	        showDropdowns: true
	    });
	});
</script>
@endsection

@section('content')

	<h1>Libur - Buat</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.holiday.store') }}" method="post" enctype="multipart/form-data">

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
				<label for="type" class="control-label col-md-3 col-sm-3 col-xs-12">Tipe <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="radio-inline"><input type="radio" id="type-libur" name="type" value="libur" @if(old('type') != '' && old('type') == 'libur') checked @endif>Libur</label> 
					<label class="radio-inline"><input type="radio" id="type-cuti" name="type" value="cuti" @if(old('type') != '' && old('type') == 'cuti') checked @endif>Cuti Bersama</label> 
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('type') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.holiday') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection