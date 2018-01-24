@extends('backend.layout.master')

@section('title')
	Shift - Edit
@endsection

@section('script')
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>


<script>
	$(function() {
	    var table = $('#datatable').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('admin.shift.datatablesDetail', ['id' => $index->id]) }}",
				type: "post",
			},
			columns: [
				{data: 'check', orderable: false, searchable: false, sClass: 'nowarp-cell'},
				{data: 'day'},
				{data: 'shift_in'},
				{data: 'shift_out'},
				{data: 'action', orderable: false, searchable: false, sClass: 'nowarp-cell'},
			],
			initComplete: function () {
				this.api().columns().every(function () {
					var column = this;
					var input = document.createElement("input");
					$(input).appendTo($(column.footer()).empty())
					.on('keyup', function () {
						column.search($(this).val(), false, false, true).draw();
					});
				});
			},
			scrollY: "400px",
			// scrollX: true,
			
		});

		$(".check-all").click(function(){
	    	if ($(this).is(':checked'))
	    	{
		        $('.' + $(this).attr('data-target')).prop('checked', true);
		    }
		    else
		    {
		    	$('.' + $(this).attr('data-target')).prop('checked', false);
		    }
	    });

	    $('#datatable').on('click', '.delete-shiftDetail', function(){
			$('.id_shift_detail-ondelete').val($(this).data('id'));
		});

	});
</script>
@endsection

@section('css')
<link href="{{ asset('backend/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('backend/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
<style type="text/css">
	.nowarp-cell{
		white-space: nowrap;
	}
</style>
@endsection

@section('content')

	@can('edit-shift')
	{{-- Delete Shift --}}
	<div id="delete-shiftDetail" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.shift.deleteDetail') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Hapus shift detail?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_shift_detail-ondelete" value="{{old('id')}}">
						<button type="submit" class="btn btn-danger">Hapus</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@endcan

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
				<label for="day_per_month" class="control-label col-md-3 col-sm-3 col-xs-12">Hari per bulan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="day_per_month" name="day_per_month" class="form-control {{$errors->first('day_per_month') != '' ? 'parsley-error' : ''}}" value="{{ old('day_per_month') == '' ? $index->day_per_month : old('day_per_month') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('day_per_month') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="work_in_holiday" class="control-label col-md-3 col-sm-3 col-xs-12">Kerja dihari libur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline">
						<input type="checkbox" id="work_in_holiday" name="work_in_holiday" class="{{$errors->first('work_in_holiday') != '' ? 'parsley-error' : ''}}" @if(old('work_in_holiday') || $index->work_in_holiday) checked @endif>
						Ya
					</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('work_in_holiday') }}</li>
					</ul>
				</div>
			</div>


			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.shift') }}">Batal</a>
					@can('edit-shift')
					<button type="submit" class="btn btn-success">Submit</button>
					@endcan
				</div>
			</div>

		</form>
	</div>

	<div class="x_panel">
		<form method="post" id="action" action="{{ route('admin.shift.actionDetail') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
			@can('create-shift')
			<a href="{{ route('admin.shift.createDetail', ['id' => $index->id]) }}" class="btn btn-default">Buat Baru</a>
			@endcan
			<select class="form-control" name="action">
				<!-- <option value="enable">Enable</option>
				<option value="disable">Disable</option> -->
				<option value="delete">Hapus</option>
			</select>
			<button type="submit" class="btn btn-success">Terapkan yang dipilih</button>
		</form>

		<table class="table table-striped table-bordered" id="datatable">
			<thead>
				<tr>
					<th width="100" nowrap>
						<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
					</th>
					<th>Hari</th>
					<th>Jam Masuk</th>
					<th>Jam Pulang</th>
					<th>Aksi</th>
				</tr>
			</thead>
		</table>
	</div>
	

@endsection