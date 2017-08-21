@extends('backend.layout.master')

@section('title')
	Gallery Management - Buat
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('backend/vendors/ckeditor/ckeditor.js') }}"></script>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace( 'description' );
    CKEDITOR.replace( 'short_description' );

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

	<h1>Gallery Management - Buat</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.gallery.store') }}" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="for" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="name" class="form-control {{$errors->first('name') != '' ? 'parsley-error' : ''}}" value="{{ old('name') }}" onchange="autoUrl(this.id, 'slug');">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('name') }}</li>
					</ul>
				</div>
				
			</div>
			<div class="form-group">
				<label for="slug" class="control-label col-md-3 col-sm-3 col-xs-12">Slug <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="slug" name="slug" class="form-control {{$errors->first('slug') != '' ? 'parsley-error' : ''}}" value="{{ old('slug') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('slug') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="description" class="control-label col-md-3 col-sm-3 col-xs-12">Description / Content</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea class="form-control {{$errors->first('description') != '' ? 'parsley-error' : ''}}" name="description" id="description">{{ old('description') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('description') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="short_description" class="control-label col-md-3 col-sm-3 col-xs-12">Short Description</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea class="form-control {{$errors->first('short_description') != '' ? 'parsley-error' : ''}}" name="short_description" id="short_description">{{ old('short_description') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('short_description') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="image" class="control-label col-md-3 col-sm-3 col-xs-12">Image</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="file" id="image" name="image" class="form-control {{$errors->first('image') != '' ? 'parsley-error' : ''}}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('image') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="date" name="date" class="form-control {{$errors->first('date') != '' ? 'parsley-error' : ''}}" value="{{old('date') != '' ? date('d F Y', strtotime(old('date'))) : '' }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('date') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="priority" class="control-label col-md-3 col-sm-3 col-xs-12">Priority</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="priority" name="priority" class="form-control {{$errors->first('priority') != '' ? 'parsley-error' : ''}}" value="{{ old('priority') == '' ? 0 : old('priority')}}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('priority') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="flag_publish" class="control-label col-md-3 col-sm-3 col-xs-12">Publish</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="flag_publish" name="flag_publish" class="form-control {{$errors->first('flag_publish') != '' ? 'parsley-error' : ''}}">
						<option value="1" @if(old('flag_publish') != '' && old('flag_publish') == 1) selected @endif>Enable</option>
						<option value="0" @if(old('flag_publish') != '' && old('flag_publish') == 0) selected @endif>Disable</option>
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('flag_publish') }}</li>
					</ul>
				</div>
			</div>
			
			<div class="ln_solid"></div>
			<h2>Meta Data</h2>

			<div class="form-group">
				<label for="meta_title" class="control-label col-md-3 col-sm-3 col-xs-12">Meta Title</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="meta_title" name="meta_title" class="form-control {{$errors->first('meta_title') != '' ? 'parsley-error' : ''}}" value="{{ old('meta_title') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('meta_title') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="meta_description" class="control-label col-md-3 col-sm-3 col-xs-12">Meta Description</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea class="form-control {{$errors->first('meta_description') != '' ? 'parsley-error' : ''}}" name="meta_description" id="meta_description">{{ old('meta_description') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('meta_description') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="meta_image" class="control-label col-md-3 col-sm-3 col-xs-12">Meta Image</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="file" id="meta_image" name="meta_image" class="form-control {{$errors->first('meta_image') != '' ? 'parsley-error' : ''}}" value="{{ old('meta_image') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('meta_image') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="meta_url" class="control-label col-md-3 col-sm-3 col-xs-12">Meta URL</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="meta_url" name="meta_url" class="form-control {{$errors->first('meta_url') != '' ? 'parsley-error' : ''}}" value="{{ old('meta_url') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('meta_url') }}</li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="meta_keyword" class="control-label col-md-3 col-sm-3 col-xs-12">Meta Keyword</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="meta_keyword" name="meta_keyword" class="form-control {{$errors->first('meta_keyword') != '' ? 'parsley-error' : ''}}" value="{{ old('meta_keyword') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('meta_keyword') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.gallery') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection