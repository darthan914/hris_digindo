@extends('backend.layout.master')

@section('title')
	Lembur
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

	<h1>Lembur</h1>
	<div class="x_panel" style="overflow: auto;">
		<form method="post" id="action" action="{{ route('admin.overtime.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
			<a href="{{ route('admin.overtime.create') }}" class="btn btn-default">Buat Baru</a>
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
					<th>Karyawan</th>
					<th>Tanggal</th>
					<th>Lembur</th>
					<th>Catatan</th>
					<th>Konfirmasi dari kepala</th>
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
					<td>{{ $list->employee->name }}</td>
					<td>{{ date('d F Y', strtotime($list->date)) }}</td>
					<td>{{ date('d F Y H:i', strtotime($list->end_overtime)) }}</td>
					<td>{{ $list->note }}</td>
					<td>{{ $list->check_leader == 1 ? 'Yes' : 'No' }}</td>
					<td nowrap>
						<a href="{{ route('admin.overtime.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
						<a href="{{ route('admin.overtime.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	

@endsection