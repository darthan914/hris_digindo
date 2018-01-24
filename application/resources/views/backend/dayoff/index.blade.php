@extends('backend.layout.master')

@section('title')
	Cuti
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/fullcalendar/dist/fullcalendar.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/fullcalendar/dist/fullcalendar.min.css') }}" />
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script type="text/javascript">
	$(function() {
		var table = $('#datatable').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('admin.dayoff.datatables') }}",
				type: "post",
				data: {
					f_id_employee : $('*[name=f_id_employee]').val(),
					f_year        : $('*[name=f_year]').val(),
				},
			},
			columns: [
				{data: 'check', orderable: false, searchable: false},

				{data: 'name'},
				{data: 'job_title'},
				{data: 'shift'},

				{data: 'date'},
				{data: 'total_dayoff'},
				{data: 'start_dayoff'},

				{data: 'end_dayoff'},
				{data: 'type'},
				{data: 'note'},

				{data: 'check_leader'},

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

		var tableRemain = $('#datatable-remain').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('admin.dayoff.datatablesRemain') }}",
				type: "post",
				data: {
					f_year : $('*[name=f_year]').val(),
				},
			},
			columns: [
				{data: 'check', orderable: false, searchable: false},

				{data: 'name'},
				{data: 'dayoff_this_year'},
				{data: 'dayoff_holiday'},

				{data: 'dayoff_remain_last_year'},
				{data: 'dayoff_remain'},

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

		$('#datatable').on('click', '.delete-dayoff', function(){
			$('.id_dayoff-ondelete').val($(this).data('id'));
		});

		$('#datatable').on('click', '.confirm-dayoff', function(){
			$('.id_dayoff-onconfirm').val($(this).data('id'));
		});

		$('#datatable').on('click', '.cancel-dayoff', function(){
			$('.id_dayoff-oncancel').val($(this).data('id'));
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

	@can('delete-dayoff')
	{{-- Delete Dayoff --}}
	<div id="delete-dayoff" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.dayoff.delete') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Hapus Cuti?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_dayoff-ondelete" value="{{old('id')}}">
						<button type="submit" class="btn btn-danger">Hapus</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@endcan

	@can('confirm-dayoff')
	{{-- Confirm Dayoff --}}
	<div id="confirm-dayoff" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.dayoff.confirm') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Terima Permintaan Cuti?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_dayoff-onconfirm" value="{{old('id')}}">
						<button type="submit" class="btn btn-success">Ya</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="cancel-dayoff" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.dayoff.confirm') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Batal Permintaan Cuti?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_dayoff-oncancel" value="{{old('id')}}">
						<button type="submit" class="btn btn-warning">Ya</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@endcan

	<h1>Cuti</h1>
	<div class="x_panel" style="overflow: auto;">
		<form method="post" id="action" action="{{ route('admin.dayoff.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
			@can('create-dayoff')
			<a href="{{ route('admin.dayoff.create') }}" class="btn btn-default">Buat Baru</a>
			@endcan
			<select class="form-control" name="action">
				<option value="delete">Hapus</option>
			</select>
			<button type="submit" class="btn btn-success">Terapkan yang dipilih</button>
		</form>

		<ul class="nav nav-tabs">
			<li @if($tab == 'index') class="active" @endif><a data-toggle="tab" href="#index">Semua</a></li>
			<li @if($tab == 'remain') class="active" @endif><a data-toggle="tab" href="#remain">Sisa cuti karyawan</a></li>
		</ul>

		<div class="tab-content">
			<div id="index" class="tab-pane fade @if($tab == 'index') in active @endif">
				<form method="get" id="filter-index" class="form-inline text-right">
					<select name="f_id_employee" class="form-control" onchange="this.form.submit()">
						<option value="">-- Filter Karyawan --</option>
						@foreach($employee as $list)
						<option value="{{ $list->id }}" @if($request->f_id_employee == $list->id) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
					<select name="f_year" class="form-control" onchange="this.form.submit()">
						<option value="">-- Filter Tahun --</option>
						@foreach($year as $list)
						<option value="{{ $list->year }}" @if($request->f_year == $list->year) selected @endif>{{ $list->year }}</option>
						@endforeach
					</select>
					<input type="hidden" name="tab" value="index">
				</form>

				<table class="table table-striped table-bordered" id="datatable">
					<thead>
						<tr>
							<th width="100" nowrap>
								<label class="checkbox-inline"><input type="checkbox" data-target="check-index" class="check-all" id="check-all">Pilih Semua</label>
							</th>

							<th>Nama</th>
							<th>Posisi</th>
							<th>Shift</th>

							<th>Tanggal Permintaan</th>
							<th>Total Cuti</th>
							<th>Dari Tanggal</th>

							<th>Ke Tanggal</th>
							<th>Tipe</th>
							<th>Catatan</th>

							<th>Konfirmasi</th>

							<th>Action</th>
						</tr>
					</thead>
				</table>
			</div>

			<div id="remain" class="tab-pane fade @if($tab == 'remain') in active @endif">
				<form method="get" id="filter-index" class="form-inline text-right">
					<select name="f_year" class="form-control" onchange="this.form.submit()">
						<option value="">-- Tahun Ini --</option>
						@foreach($year as $list)
						<option value="{{ $list->year }}" @if($request->f_year == $list->year) selected @endif>{{ $list->year }}</option>
						@endforeach
					</select>
					<input type="hidden" name="tab" value="remain">
				</form>

				{{-- <p>Total Cuti Bersama {{ $request->f_year ? $request->f_year : date('Y') }} : {{ $totalDayoff->first()->total_holiday }}</p> --}}

				<table class="table table-striped table-bordered" id="datatable-remain">
					<thead>
						<tr>
							<th width="100" nowrap>
								<label class="checkbox-inline"><input type="checkbox" data-target="check-remain" class="check-all" id="check-all">Pilih Semua</label>
							</th>

							<th>Nama</th>
							<th>Total Cuti</th>
							<th>Libur Cuti</th>

							<th>Utang Cuti</th>
							<th>Sisa Cuti</th>

							<th>Action</th>
						</tr>
					</thead>
					
				</table>
			</div>
		</div>
	</div>
	

@endsection