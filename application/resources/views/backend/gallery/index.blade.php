@extends('backend.layout.master')

@section('title')
	Gallery Management
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

	<h1>Gallery Management</h1>
	<div class="x_panel" style="overflow: auto;">
		<form method="post" id="action" action="{{ route('admin.gallery.action') }}" class="form-inline text-right">
			<a href="{{ route('admin.gallery.create') }}" class="btn btn-default">Buat Baru</a>
			<select class="form-control" name="action">
				<option value="enable">Enable</option>
				<option value="disable">Disable</option>
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
					<th>Nama</th>
					<th>Slug</th>
					<th>Tanggal</th>
					<th>Image</th>
					<th>Priority</th>
					<th>Publish</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@php $count=0; @endphp
				@foreach($index as $list)
				<tr>
					<td class="a-center ">
						<input type="checkbox" class="check" value="{{ $list->id }}" name="id[]" form="action">
					</td>
					<td>{{ ++$count }}</td>
					<td>{{ $list->name }}</td>
					<td>{{ $list->slug }}</td>
					<td>{{ date('d F Y', strtotime($list->date)) }}</td>
					<td>@if($list->image != '')<img src="{{ asset($list->image) }}" style="height: 100px;"/>@endif</td>
					<td>{{ $list->priority }}</td>
					<td>
						@if($list->flag_publish == 1)
							<a href="{{ route('admin.gallery.publish', ['id' => $list->id, 'action' => 0]) }}" class="btn btn-xs btn-success" onclick="return confirm('Disable this data?')"><i class="fa fa-check" aria-hidden="true"></i></a>
						@else
							<a href="{{ route('admin.gallery.publish', ['id' => $list->id, 'action' => 1]) }}" class="btn btn-xs btn-default" onclick="return confirm('Enable this data?')"><i class="fa fa-times"></i></a>
						@endif
					</td>	
					<td nowrap>
						<a href="{{ route('admin.gallery.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
						<a href="{{ route('admin.gallery.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	

@endsection