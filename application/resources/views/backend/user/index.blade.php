@extends('backend.layout.master')

@section('title')
	Daftar User
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
				url: "{{ route('admin.user.datatables') }}",
				type: "post",
			},
			columns: [
				{data: 'check', orderable: false, searchable: false},
				{data: 'username'},
				{data: 'email'},
				{data: 'name'},
				{data: 'id_role'},
				{data: 'grant'},
				{data: 'denied'},
				{data: 'active'},
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

		$('#datatable').on('click', '.delete-user', function(){
			$('.id_user-ondelete').val($(this).data('id'));
		});
		$('#datatable').on('click', '.impersonate-user', function(){
			$('.id_user-ontake').val($(this).data('id'));
		});
		$('#datatable').on('click', '.active-user', function(){
			$('.id_user-onactive').val($(this).data('id'));
		});
		$('#datatable').on('click', '.inactive-user', function(){
			$('.id_user-oninactive').val($(this).data('id'));
		});

		@if(Session::has('impersonate-user-error'))
		$('#impersonate-user').modal('show');
		@endif
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

	<h1>Daftar User</h1>
	@can('delete-user')
	{{-- Delete User --}}
	<div id="delete-user" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.user.delete') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Hapus User?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_user-ondelete" value="{{old('id')}}">
						<button type="submit" class="btn btn-danger">Hapus</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@endcan

	@can('impersonate-user')
	{{-- Impersonate User --}}
	<div id="impersonate-user" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.user.impersonate') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Password untuk ambil ahli</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="password" class="control-label col-md-3 col-sm-3 col-xs-12">Password User<span class="required">*</span>
							</label>
							<div class="col-md-9 col-sm-9 col-xs-12">
								<input type="password" id="password" name="password" class="form-control {{$errors->first('password') != '' ? 'parsley-error' : ''}}">
								<ul class="parsley-errors-list filled">
									<li class="parsley-required">{{ $errors->first('password') }}</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_user-ontake" value="{{old('id')}}">
						<button type="submit" class="btn btn-success">Ambil ahli</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@endcan

	@can('active-user')
	{{-- Active User --}}
	<div id="active-user" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.user.active') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Aktifkan User?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_user-onactive" value="{{old('id')}}">
						<button type="submit" class="btn btn-success">Aktif</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{-- Inactive User --}}
	<div id="inactive-user" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.user.active') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Non aktif User?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_user-oninactive" value="{{old('id')}}">
						<button type="submit" class="btn btn-dark">Non aktif</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@endcan

	<div class="x_panel" style="overflow: auto;">
		<form method="post" id="action" action="{{ route('admin.user.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin?')">
			@can('create-user')
			<a href="{{ route('admin.user.create') }}" class="btn btn-default">Buat</a>
			@endcan
			<select class="form-control" name="action">
				<option value="enable">Aktif</option>
				<option value="disable">Non Aktif</option>
				<option value="delete">Hapus</option>
			</select>
			<button type="submit" class="btn btn-success">Terapkan dipilih</button>
		</form>

		<table class="table table-striped table-bordered" id="datatable">
			<thead>
				<tr>
					<th>
						<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih</label>
					</th>

					<th>Username</th>
					<th>Email</th>
					<th>Nama</th>

					<th>Role</th>
					<th>Terima</th>
					<th>Tolak</th>

					<th>Aktif</th>
					<th>Aksi</th>
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