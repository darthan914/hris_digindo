@extends('backend.layout.master')

@section('title')
	Laporan Absen - {{ $absence->name }}
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

	<h1>Laporan Absen - {{ $absence->name }}</h1>
	<div class="x_panel" style="overflow: auto;">
		<div class="row">
			<div class="col-md-6">
				<form method="get" class="form-inline">
					
				</form>
			</div>
			<div class="col-md-6">
				<form method="post" id="action" action="{{ route('admin.absence.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
					<a href="{{ route('admin.absence.create') }}" class="btn btn-default">Buat Baru</a>
					<select class="form-control" name="action">
						<!-- <option value="enable">Enable</option>
						<option value="disable">Disable</option> -->
						<option value="delete">Hapus</option>
					</select>
					<button type="submit" class="btn btn-success">Terapkan yang dipilih</button>
				</form>
			</div>
		</div>
				

		<table class="table table-striped table-bordered" id="datatable-buttons">
			<thead>
				<tr>
					<th width="100" nowrap>
						<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
					</th>
					<th>No.</th>
					<th>Nama</th>
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
					<td>{{ $list->employee->name or 'tidak diset id : '.$list->id_machine }}</td>
					<td nowrap>
						@if(!empty($list->employee->name))
						<a href="{{ route('admin.absence.employeeDetail', ['id' => $list->id]) }}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
						@else
						<a href="#" class="btn btn-xs btn-dark"><i class="fa fa-eye-slash"></i></a>
						@endif
						<a href="{{ route('admin.absence.delete', ['id' => $list->id, 'type' => 'absenceEmployee']) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	

@endsection