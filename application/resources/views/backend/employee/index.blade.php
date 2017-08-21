@extends('backend.layout.master')

@section('title')
	Data Karyawan
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

	<h1>Data Karyawan</h1>
	<div class="x_panel" style="overflow: auto;">
		<form method="post" id="action" action="{{ route('admin.employee.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
			<a href="{{ route('admin.employee.create') }}" class="btn btn-default">Buat Baru</a>
			<select class="form-control" name="action">
				<!-- <option value="enable">Enable</option>
				<option value="disable">Disable</option> -->
				<option value="delete">Hapus</option>
			</select>
			<button type="submit" class="btn btn-success">Terapkan yang dipilih</button>
		</form>
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#all">Semua</a></li>
			<li><a data-toggle="tab" href="#active">Active</a></li>
			<li><a data-toggle="tab" href="#resign">Resign</a></li>
		</ul>
		<div class="tab-content">
    		<div id="all" class="tab-pane fade in active">
				<table class="table table-striped table-bordered" id="datatable-buttons">
					<thead>
						<tr>
							<th width="100" nowrap>
								<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
							</th>
							<th>No.</th>
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
					<tbody>
						@php $count=0; @endphp
						@foreach($index as $list)
						<tr>
							<td class="a-center ">
								<input type="checkbox" class="check" value="{{ $list->id }}" name="id[]" form="action">
							</td>
							<td>{{ ++$count }}</td>
							<td>{{ $list->name }}</td>
							<td>{{ $list->nik }}</td>
							<td>{{ $list->jobTitle->name }}</td>
							<td>{{ $list->division }}</td>
							<td>{{ $list->sub_division }}</td>
							<td>{{ date('d F Y', strtotime($list->date_join)) }}</td>
							<td>{{ $list->date_resign == '' ? 'Active' : 'Resign at ' . date('d F Y', strtotime($list->date_resign)) }}</td>
							<td nowrap>
								<a href="{{ route('admin.employee.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>
								<a href="{{ route('admin.employee.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>

    		</div>
    		<div id="active" class="tab-pane fade">
    			<table class="table table-striped table-bordered" id="datatable-buttons2">
					<thead>
						<tr>
							<th width="100" nowrap>
								<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
							</th>
							<th>No.</th>
							<th>Nama</th>
							<th>NIK</th>
							<th>Posisi</th>
							<th>Divisi</th>
							<th>Sub Divisi</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@php $count=0; @endphp
						@foreach($active as $list)
						<tr>
							<td class="a-center ">
								<input type="checkbox" class="check" value="{{ $list->id }}" name="id[]" form="action">
							</td>
							<td>{{ ++$count }}</td>
							<td>{{ $list->name }}</td>
							<td>{{ $list->nik }}</td>
							<td>{{ $list->jobTitle->name }}</td>
							<td>{{ $list->division }}</td>
							<td>{{ $list->sub_division }}</td>
							<td>{{ $list->date_resign == '' ? 'Active' : 'Resign at ' . date('d F Y', strtotime($list->date_resign)) }}</td>
							<td nowrap>
								<a href="{{ route('admin.employee.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>
								<a href="{{ route('admin.employee.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
    		</div>
    		<div id="resign" class="tab-pane fade">
    			<table class="table table-striped table-bordered" id="datatable-buttons3">
					<thead>
						<tr>
							<th width="100" nowrap>
								<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
							</th>
							<th>No.</th>
							<th>Nama</th>
							<th>NIK</th>
							<th>Posisi</th>
							<th>Divisi</th>
							<th>Sub Divisi</th>
							<th>Tanggal Resign</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@php $count=0; @endphp
						@foreach($resign as $list)
						<tr>
							<td class="a-center ">
								<input type="checkbox" class="check" value="{{ $list->id }}" name="id[]" form="action">
							</td>
							<td>{{ ++$count }}</td>
							<td>{{ $list->name }}</td>
							<td>{{ $list->nik }}</td>
							<td>{{ $list->jobTitle->name }}</td>
							<td>{{ $list->division }}</td>
							<td>{{ $list->sub_division }}</td>
							<td>{{ date('d F Y', strtotime($list->date_resign)) }}</td>
							<td nowrap>
								<a href="{{ route('admin.employee.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>
								<a href="{{ route('admin.employee.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
    		</div>
    	</div>
				
	</div>
	

@endsection