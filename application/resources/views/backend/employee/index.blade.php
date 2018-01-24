@extends('backend.layout.master')

@section('title')
	Data Karyawan
@endsection

@section('script')
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script type="text/javascript">
	$(function() {
		var table = $('#datatable').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('admin.employee.datatables') }}",
				type: "post",
				data: {
					f_status  : $('*[name=f_status]').val(),
				},
			},
			columns: [
				{data: 'check', orderable: false, searchable: false},
				{data: 'name'},
				{data: 'nik'},
				{data: 'job_title'},
				{data: 'division'},
				{data: 'sub_division'},
				{data: 'date_join'},
				{data: 'date_resign'},
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
			// scrollY: "400px",
			// scrollX: true,
			
		});

		$('#datatable').on('click', '.delete-employee', function(){
			$('.id_employee-ondelete').val($(this).data('id'));
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
	});
</script>
@endsection

@section('css')
<link href="{{ asset('backend/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('backend/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('content')

	@can('delete-employee')
	{{-- Delete Employee --}}
	<div id="delete-employee" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.employee.delete') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Hapus Karyawan?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_employee-ondelete" value="{{old('id')}}">
						<button type="submit" class="btn btn-danger">Hapus</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@endcan

	<h1>Data Karyawan</h1>
	<div class="x_panel" style="overflow: auto;">
		<div class="row">
			<div class="col-md-6">
				<form class="form-inline" method="get">
					<select name="f_status" class="form-control" onchange="this.form.submit()">
						<option value="" {{ $request->f_status === '' ? 'selected' : '' }}>Semua Status</option>
						<option value="active" {{ $request->f_status === 'active' ? 'selected' : '' }}>Aktif</option>
						<option value="resign" {{ $request->f_status === 'resign' ? 'selected' : '' }}>Resign</option>
					</select>
				</form>
			</div>
			<div class="col-md-6">
				<form method="post" id="action" action="{{ route('admin.employee.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
					@can('create-employee')
					<a href="{{ route('admin.employee.create') }}" class="btn btn-default">Buat Baru</a>
					@endcan
					<select class="form-control" name="action">
						<!-- <option value="enable">Enable</option>
						<option value="disable">Disable</option> -->
						<option value="delete">Hapus</option>
					</select>
					<button type="submit" class="btn btn-success">Terapkan yang dipilih</button>
				</form>
			</div>
		</div>
				

		

		<div class="ln_solid"></div>

		<table class="table table-striped table-bordered" id="datatable">
			<thead>
				<tr>
					<th width="100" nowrap>
						<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
					</th>

					<th>Nama</th>
					<th>NIK</th>
					<th>Posisi</th>

					<th>Divisi</th>
					<th>Sub Divisi</th>
					<th>Tanggal Bergabung</th>

					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td></td>

					<td></td>
					<td></td>
					<td></td>

					<td></td>
					<td></td>
					<td></td>

					<td></td>
					<td></td>

				</tr>
			</tfoot>
		</table>
				
	</div>
	

@endsection