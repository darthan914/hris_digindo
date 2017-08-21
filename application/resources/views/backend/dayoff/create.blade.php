@extends('backend.layout.master')

@section('title')
	Cuti - Buat
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
	    $('input[name="dayoff"]').daterangepicker({
	    	locale: {
		      format: 'DD MMMM YYYY'
		    },
	        autoApply: true,
	    });

	    $('select[name="id_employee"]').select2();
	});
</script>
@endsection

@section('content')

	<h1>Cuti - Buat</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.dayoff.store') }}" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="id_employee" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="id_employee" name="id_employee" class="form-control {{$errors->first('id_employee') != '' ? 'parsley-error' : ''}}" value="{{ old('id_employee') }}">
						<option value="">-- Pilih Karyawan --</option>
						@foreach($employee as $list)
						<option value="{{ $list->id }}" @if(old('id_employee') != '' && old('id_employee') == $list->id) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('id_employee') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Permintaan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="date" name="date" class="form-control {{$errors->first('date') != '' ? 'parsley-error' : ''}}" value="{{ old('date') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('date') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="half_day" class="control-label col-md-3 col-sm-3 col-xs-12">Cuti Setengah Hari
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline">
						<input type="checkbox" id="half_day" name="half_day" class="{{$errors->first('half_day') != '' ? 'parsley-error' : ''}}" @if(old('half_day')) checked @endif>
						Cuti Setengah Hari (Berlaku cuti sehari)
					</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('half_day') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="dayoff" class="control-label col-md-3 col-sm-3 col-xs-12">Cuti <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="dayoff" name="dayoff" class="form-control {{$errors->first('dayoff') != '' ? 'parsley-error' : ''}}" value="{{ old('dayoff') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('dayoff') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="type" class="control-label col-md-3 col-sm-3 col-xs-12">Tipe <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="radio-inline"><input type="radio" id="type-cuti" name="type" value="cuti" @if(old('type') != '' && old('type') == 'cuti') checked @endif>Cuti</label> 
					<label class="radio-inline"><input type="radio" id="type-izin" name="type" value="izin" @if(old('type') != '' && old('type') == 'izin') checked @endif>Izin</label> 
					<label class="radio-inline"><input type="radio" id="type-sakit" name="type" value="sakit" @if(old('type') != '' && old('type') == 'sakit') checked @endif>Sakit</label> 
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('type') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="note" class="control-label col-md-3 col-sm-3 col-xs-12">Catatan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea id="note" name="note" class="form-control {{$errors->first('note') != '' ? 'parsley-error' : ''}}">{{ old('note') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('note') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.dayoff') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection