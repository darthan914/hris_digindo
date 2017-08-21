@extends('backend.layout.master')

@section('title')
	Nama Pekerjaan - Edit
@endsection

@section('script')
<script type="text/javascript">
	$(function() {
	    $('select[name="id_job_title"], select[name="id_shift"]').select2();
	});
</script>
@endsection

@section('content')

	<h1>Nama Pekerjaan - Edit</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.attendance.update', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="id_job_title" class="control-label col-md-3 col-sm-3 col-xs-12">Posisi <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="id_job_title" name="id_job_title" class="form-control {{$errors->first('id_job_title') != '' ? 'parsley-error' : ''}}">
						<option value="">-- Pilih Posisi --</option>
						@foreach($jobTitle as $list)
						<option value="{{ $list->id }}" @if(old('id_job_title') != '' && old('id_job_title') == $list->id) selected @elseif($index->id_job_title == $list->id ) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('id_job_title') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="id_shift" class="control-label col-md-3 col-sm-3 col-xs-12">Shift <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="id_shift" name="id_shift" class="form-control {{$errors->first('id_shift') != '' ? 'parsley-error' : ''}}">
						<option value="">-- Pilih Shift --</option>
						@foreach($shift as $list)
						<option value="{{ $list->id }}" @if(old('id_shift') != '' && old('id_shift') == $list->id) selected @elseif($index->id_shift == $list->id ) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('id_shift') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.attendance') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection