@extends('backend.layout.master')

@section('title')
	User Management
@endsection

@section('script')
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script type="text/javascript">
	$(function() {
		$('#datatable-buttons').DataTable({
			"columnDefs": [
			    { "orderable": false, "targets": 0 }
			]
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

@section('content')

	<h1>User Management</h1>
	<div class="x_panel" style="overflow: auto;">
		@if(Auth::user()->access == 0)
		<form method="post" id="action" action="{{ route('admin.user.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
			<a href="{{ route('admin.user.create') }}" class="btn btn-default">Buat Baru</a>
			<select class="form-control" name="action">
				<option value="enable">Active</option>
				<option value="disable">Incative</option>
				<option value="delete">Hapus</option>
			</select>
			<button type="submit" class="btn btn-success">Terapkan yang dipilih</button>
		</form>
		@endif

		<table class="table table-striped table-bordered" id="datatable-buttons">
			<thead>
				<tr>
					@if(Auth::user()->access == 0)
					<th width="100" nowrap>
						<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
					</th>
					@endif
					<th>No.</th>
					<th>Nama</th>
					<th>Username</th>
					<th>Email</th>
					<th>Status</th>
					<th>Access</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@php $count=0; @endphp
				@foreach($index as $list)
				<tr>
					@if(Auth::user()->access == 0)
					<td class="a-center ">
						@if(Auth::id() != $list->id)
						<input type="checkbox" class="check" value="{{ $list->id }}" name="id[]" form="action">
						@endif
					</td>
					@endif
					<td>{{ ++$count }}</td>
					<td>{{ $list->name }}</td>
					<td>{{ $list->username }}</td>
					<td>{{ $list->email }}</td>
					<td>
						@if(Auth::user()->access == 0 && Auth::id() != $list->id)
							@if($list->active == 1)
								<a href="{{ route('admin.user.status', ['id' => $list->id, 'action' => 0]) }}" class="btn btn-xs btn-success" onclick="return confirm('Inactive this data?')"><i class="fa fa-check" aria-hidden="true"></i></a>
							@else
								<a href="{{ route('admin.user.status', ['id' => $list->id, 'action' => 1]) }}" class="btn btn-xs btn-default" onclick="return confirm('Active this data?')"><i class="fa fa-times"></i></a>
							@endif
						@else
							@if($list->active == 1)
								<a href="#" class="btn btn-xs btn-success" disabled><i class="fa fa-check" aria-hidden="true"></i></a>
							@else
								<a href="#" class="btn btn-xs btn-default" disabled><i class="fa fa-times"></i></a>
							@endif
						@endif
					</td>
					<td>
						@if($list->access == 0)
							Semua
						@else
							Semua, Kecuali Buat User dan Ubah User Lain
						@endif
					</td>	
					<td nowrap>
						@if(Auth::user()->access == 0 || Auth::id() == $list->id)
						<a href="{{ route('admin.user.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
						@endif
						@if(Auth::user()->access == 0 && Auth::id() != $list->id)
						<a href="{{ route('admin.user.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	

@endsection