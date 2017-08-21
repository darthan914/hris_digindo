@extends('backend.layout.master')

@section('title')
	Libur
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/fullcalendar/dist/fullcalendar.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/fullcalendar/dist/fullcalendar.min.css') }}" />
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

	<h1>Libur</h1>
	<div class="x_panel" style="overflow: auto;">
		<div class="row">
			<div class="col-md-6">
				<form method="get" id="filter-index" class="form-inline">
					<select name="f_year" class="form-control" onchange="this.form.submit()">
						<option value="">-- Filter Tahun --</option>
						@foreach($year as $list)
						<option value="{{ $list->year }}" @if($f_year == $list->year) selected @endif>{{ $list->year }}</option>
						@endforeach
					</select>
				</form>
			</div>
			<div class="col-md-6">
				<form method="post" id="action" action="{{ route('admin.holiday.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
					<a href="{{ route('admin.holiday.create') }}" class="btn btn-default">Buat Baru</a>
					<select class="form-control" name="action">
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
					<th>Tanggal</th>
					<th>Tipe</th>
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
					<td>{{ date('d F Y', strtotime($list->date)) }}</td>
					<td>{{ $list->type == 'libur' ? 'Libur' : 'Cuti Bersama' }}</td>
					<td nowrap>
						<a href="{{ route('admin.holiday.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
						<a href="{{ route('admin.holiday.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

	</div>
	

@endsection