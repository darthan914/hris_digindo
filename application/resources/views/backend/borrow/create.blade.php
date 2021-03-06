@extends('backend.layout.master')

@section('title')
	Peminjaman Barang - Buat
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('backend/vendors/ckeditor/ckeditor.js') }}"></script>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace( 'note' );
    // CKEDITOR.replace( 'short_description' );

    $(function() {
	    $('select[name="id_employee"]').select2();

	    $('input[name="date_borrow"], input[name="date_return"]').daterangepicker({
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

	<h1>Peminjaman Barang - Buat</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.borrow.store') }}" method="post" enctype="multipart/form-data">

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
				<label for="item" class="control-label col-md-3 col-sm-3 col-xs-12">Barang <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="item" name="item" class="form-control {{$errors->first('item') != '' ? 'parsley-error' : ''}}" value="{{ old('item') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('item') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="date_borrow" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Pinjam <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="date_borrow" name="date_borrow" class="form-control {{$errors->first('date_borrow') != '' ? 'parsley-error' : ''}}" value="{{ old('date_borrow') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('date_borrow') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="date_return" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Dikembalikan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="date_return" name="date_return" class="form-control {{$errors->first('date_return') != '' ? 'parsley-error' : ''}}" value="{{ old('date_return') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('date_return') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="status" class="control-label col-md-3 col-sm-3 col-xs-12">Status peminjaman
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline">
						<input type="checkbox" id="status" name="status" class="{{$errors->first('status') != '' ? 'parsley-error' : ''}}" @if(old('status') == 1) checked @endif value="1">
						dipinjam
					</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('status') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="note" class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea id="note" name="note" class="form-control {{$errors->first('note') != '' ? 'parsley-error' : ''}}">{!! old('note') !!}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('note') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.borrow') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection