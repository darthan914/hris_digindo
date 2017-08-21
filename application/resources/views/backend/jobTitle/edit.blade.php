@extends('backend.layout.master')

@section('title')
	Nama Pekerjaan - Edit
@endsection

@section('script')

@endsection

@section('content')

	<h1>Nama Pekerjaan - Edit</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.jobTitle.update', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="for" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="name" class="form-control {{$errors->first('name') != '' ? 'parsley-error' : ''}}" value="{{ old('name') == '' ? $index->name : old('name') }}" onchange="autoUrl(this.id, 'slug');">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('name') }}</li>
					</ul>
				</div>
				
			</div>
			<div class="form-group">
				<label for="code" class="control-label col-md-3 col-sm-3 col-xs-12">Kode <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="code" name="code" class="form-control {{$errors->first('code') != '' ? 'parsley-error' : ''}}" value="{{ old('code') == '' ? $index->code : old('code') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('code') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="book_overtime" class="control-label col-md-3 col-sm-3 col-xs-12">Butuh buku lembur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline">
						<input type="checkbox" id="book_overtime" name="book_overtime" class="{{$errors->first('book_overtime') != '' ? 'parsley-error' : ''}}" @if(old('book_overtime') || $index->book_overtime) checked @endif> Ya
					</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('book_overtime') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="min_overtime" class="control-label col-md-3 col-sm-3 col-xs-12">Minimum Lembur (menit)<span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="min_overtime" name="min_overtime" class="form-control {{$errors->first('min_overtime') != '' ? 'parsley-error' : ''}}" value="{{ old('min_overtime') == '' ? $index->min_overtime : old('min_overtime') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('min_overtime') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.jobTitle') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection