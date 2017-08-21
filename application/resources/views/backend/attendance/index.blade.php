@extends('backend.layout.master')

@section('title')
	Jadwal Posisi
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

	<h1>Jadwal Posisi</h1>
	<div class="x_panel" style="overflow: auto;">
		<div class="row">
			<div class="col-md-6">
				<form method="get" class="form-inline">
					<select name="f_id_shift" class="form-control" onchange="this.form.submit()">
						<option value="">-- Filter Shift --</option>
						@foreach($shift as $list)
						<option value="{{ $list->id }}" @if($f_id_shift == $list->id) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
				</form>
			</div>
			<div class="col-md-6">
				<form method="post" id="action" action="{{ route('admin.attendance.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
					<!-- <a href="{{ route('admin.attendance.create') }}" class="btn btn-default">Buat Baru</a> -->
					<select class="form-control" name="action">
						<option value="">-- Pilih Action --</option>
						<optgroup label="Ganti shift yang dipilih">
							@foreach($shift as $list)
							<option value="{{ $list->id }}">{{ $list->name }}</option>
							@endforeach
						</optgroup>
						<optgroup label="Action yang dipilih">
							<!-- <option value="enable">Enable</option>
							<option value="disable">Disable</option> -->
							<option value="delete">Hapus</option>
						</optgroup>
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
					<th>Posisi</th>
					<th>Shift</th>
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
					<td>{{ $list->job_title_name }}</td>
					<td><a href="{{ route('admin.shift.edit', ['id' => $list->shift_id]) }}">{{ $list->shift_name }}</a></td>
					<td nowrap>
						@if($list->id)
						<a href="{{ route('admin.attendance.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
						@else
						<a href="{{ route('admin.attendance.create', ['id_job_title' => $list->job_title_id]) }}" class="btn btn-xs btn-info"><i class="fa fa-plus"></i></a>
						@endif

						@if($list->id)
						<a href="{{ route('admin.attendance.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	

@endsection