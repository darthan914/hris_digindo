@extends('backend.layout.master')

@section('title')
	Shift - Edit
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

	<h1>Shift - Edit</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.shift.update', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">

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
				<label for="work_in_holiday" class="control-label col-md-3 col-sm-3 col-xs-12">Kerja dihari libur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline">
						<input type="checkbox" id="work_in_holiday" name="work_in_holiday" class="{{$errors->first('work_in_holiday') != '' ? 'parsley-error' : ''}}" @if(old('work_in_holiday') || $index->work_in_holiday) checked @endif>
						Libur dihari kerja
					</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('work_in_holiday') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="late" class="control-label col-md-3 col-sm-3 col-xs-12">Kelipatan Telat <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="late" name="late" class="form-control {{$errors->first('late') != '' ? 'parsley-error' : ''}}" value="{{ old('late') == '' ? $index->late : old('late') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('late') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.shift') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>

	<div class="x_panel">
		<form method="post" id="action" action="{{ route('admin.shiftDetail.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
			<a href="{{ route('admin.shiftDetail.create', ['id' => $index->id]) }}" class="btn btn-default">Buat Baru</a>
			<select class="form-control" name="action">
				<!-- <option value="enable">Enable</option>
				<option value="disable">Disable</option> -->
				<option value="delete">Hapus</option>
			</select>
			<button type="submit" class="btn btn-success">Terapkan yang dipilih</button>
		</form>

		<table class="table table-striped table-bordered" id="datatable-buttons">
			<thead>
				<tr>
					<th width="100" nowrap>
						<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
					</th>
					<th>No.</th>
					<th>Hari</th>
					<th>Masuk</th>
					<th>Pulang</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@php $count=0; @endphp
				@foreach($detail as $list)
				<tr>
					<td class="a-center ">
						<input type="checkbox" class="check" value="{{ $list->id }}" name="id[]" form="action">
					</td>
					<td>{{ ++$count }}</td>
					<td>{{ $hari[$list->day] }}</td>
					<td>{{ $list->shift_in }}</td>
					<td>{{ $list->shift_out }}</td>
					<td nowrap>
						<a href="{{ route('admin.shiftDetail.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
						<a href="{{ route('admin.shiftDetail.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	

@endsection