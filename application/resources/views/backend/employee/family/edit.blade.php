@extends('backend.layout.master')

@section('title')
	Data Karyawan - Edit
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('backend/vendors/ckeditor/ckeditor.js') }}"></script>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.

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
	    $('input[name="date_join"], input[name="birthday"], input[name="date_contract"], input[name="end_contract"]').daterangepicker({
	    	locale: {
		      format: 'DD MMMM YYYY'
		    },
	        singleDatePicker: true,
	        showDropdowns: true
	    });

	    $('select[name="id_job_title"]').select2();
	});
</script>
@endsection

@section('content')

	<h1>Data Karyawan - Family Hubungan - Edit</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.employee.updateFamily', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="relation" class="control-label col-md-3 col-sm-3 col-xs-12">Hubungan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="relation" name="relation" class="form-control {{$errors->first('relation') != '' ? 'parsley-error' : ''}}">
						<option value="">-- Select Hubungan --</option>
						<option value="ayah" @if(old('relation') != '' && old('relation') == 'ayah') selected @elseif($index->relation == 'ayah') selected @endif>Ayah</option>
						<option value="ibu" @if(old('relation') != '' && old('relation') == 'ibu') selected @elseif($index->relation == 'ibu') selected @endif>Ibu</option>
						<option value="saudara" @if(old('relation') != '' && old('relation') == 'saudara') selected @elseif($index->relation == 'saudara') selected @endif>Saudara</option>
						<option value="suami" @if(old('relation') != '' && old('relation') == 'suami') selected @elseif($index->relation == 'suami') selected @endif>Suami</option>
						<option value="istri" @if(old('relation') != '' && old('relation') == 'istri') selected @elseif($index->relation == 'istri') selected @endif>Istri</option>
						<option value="anak" @if(old('relation') != '' && old('relation') == 'anak') selected @elseif($index->relation == 'anak') selected @endif>Anak</option>
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('relation') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="name" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="name" name="name" class="form-control {{$errors->first('name') != '' ? 'parsley-error' : ''}}" value="{{ old('name') != '' ? old('name') : $index->name }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('name') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="age" class="control-label col-md-3 col-sm-3 col-xs-12">Umur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="age" name="age" class="form-control {{$errors->first('age') != '' ? 'parsley-error' : ''}}" value="{{ old('age') != '' ? old('age') : $index->age }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('age') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="school" class="control-label col-md-3 col-sm-3 col-xs-12">Pendidikan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="school" name="school" class="form-control {{$errors->first('school') != '' ? 'parsley-error' : ''}}" value="{{ old('school') != '' ? old('school') : $index->school }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('school') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="job" class="control-label col-md-3 col-sm-3 col-xs-12">Job
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="job" name="job" class="form-control {{$errors->first('job') != '' ? 'parsley-error' : ''}}" value="{{ old('job') != '' ? old('job') : $index->job }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('job') }}</li>
					</ul>
				</div>
			</div>

			

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.employee.edit', ['id' => $index->id_employee]) }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection