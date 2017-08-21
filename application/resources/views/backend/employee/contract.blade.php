@extends('backend.layout.master')

@section('title')
	Data Contract
@endsection

@section('script')
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script type="text/javascript">
	$(function() {
		$('#datatable-buttons, #datatable-buttons2, #datatable-buttons3').DataTable({
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

	<h1>Data Contract</h1>
	<div class="x_panel" style="overflow: auto;">
		<div class="row">
			<div class="col-md-6">
				<form method="get" id="filter-index" class="form-inline">
					<select name="f_id_employee" class="form-control" onchange="this.form.submit()">
						<option value="">-- Filter Karyawan --</option>
						@foreach($employee as $list)
						<option value="{{ $list->id }}" @if($f_id_employee == $list->id) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
				</form>
			</div>
			
			<div class="col-md-6">
				<form method="post" id="action" action="{{ route('admin.employeeContract.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
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
					<th>Tanggal Change</th>
					<th>Tipe Kontrak</th>
					<th>Tanggal Kontrak</th>
					<th>Akhir Kontrak</th>
					<th>Catatan</th>
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
					<td>{{ $list->employee->name or '-' }}</td>
					<td>{{ date('d F Y', strtotime($list->date_change)) }}</td>
					<td>{{ $list->type_contract }}</td>
					<td>{{ date('d F Y', strtotime($list->date_contract)) }}</td>
					<td>{{ date('d F Y', strtotime($list->end_contract)) }}</td>
					<td>{{ $list->note }}</td>
					<td nowrap>
						<a href="{{ route('admin.employeeContract.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

	</div>
	

@endsection