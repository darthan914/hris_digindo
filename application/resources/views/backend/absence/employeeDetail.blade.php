@extends('backend.layout.master')

@section('title')
	Laporan Absen - {{ $absenceEmployee->employee->name or $absenceEmployee->id_machine }}
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

	<h1>Laporan Absen - {{ $absenceEmployee->employee->name or $absenceEmployee->id_machine }}</h1>
	<div class="x_panel" style="overflow: auto;">
		<div class="row">
			<div class="col-md-6">
				<form method="get" class="form-inline">
					
				</form>
			</div>
			<div class="col-md-6">
				<form method="post" id="action" action="{{ route('admin.absence.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
					<a href="javascript:history.go(-1)" class="btn btn-default">Kembali</a>
					<a href="{{ route('admin.absence.createAbsenceEmployeeDetail', ['id' => $id ]) }}" class="btn btn-primary">Buat Baru</a>
				</form>
			</div>
		</div>

		<div class="ln_solid"></div>

		<table class="table table-striped table-bordered" id="datatable-buttons">
			<thead>
				<tr>
					{{-- <th width="100" nowrap>
						<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
					</th> --}}
					<th>No.</th>
					<th>Tanggal</th>
					<th>Masuk</th>
					<th>Pulang</th>
					<th>Status</th>
					<th>Gaji Per Bulan</th>
					<th>Gaji Per Hari</th>
					<th>Point</th>
					
					<th>Jam Lembur</th>
					<th>Bonus Lembur</th>
					<th>Potongan Uang Telat</th>
					<th>Denda Tambahan</th>
					<th>Edit</th>
					<!-- <th>Action</th> -->
				</tr>
			</thead>
			<tbody>
				@php $count=1; $total_gaji=$total_lembur=$total_telat=$total_denda=0; @endphp
				@foreach($absenceEmployeeDetail as $list)
					<tr>
						{{-- <td class="a-center ">
							<input type="checkbox" class="check" value="{{ $list->id }}" name="id[]" form="action">
						</td> --}}
						<td>{{ $count++ }}</td>
						<td>{{ date('d/m/Y', strtotime($list->schedule_in)) }}</td>
						<td>{{ date('H:i', strtotime($list->time_in)) }}</td>
						<td>{{ date('H:i', strtotime($list->time_out)) }}</td>
						<td>{{ $list->status }} : {{ $list->status_note or '' }}</td>
						<td>Rp. {{ number_format($list->gaji_pokok) }}</td>
						<td>Rp. {{ number_format($list->gaji_pokok / $present) }}</td>
						<td>
							{{ $list->gaji }}
							@php $total_gaji += ($list->gaji_pokok / $present) * $list->gaji; @endphp
						</td>
						
						<td>{{ $list->time_overtime >= '1900-01-01 00:00:00' ? date('d-m-Y H:i', strtotime($list->time_overtime)) : '-' }}</td>
						<td>Rp. {{ number_format($total_lembur += $list->point_overtime * $list->payment_overtime) }} ({{ $list->point_overtime }})</td>
						<td>Rp. {{ number_format($total_telat += $list->total_late * $list->fine_late) }} ({{$list->total_late}})</td>
						<td>Rp. {{ number_format($total_denda +=$list->fine_additional) }}</td>
						
						<td nowrap>
							<a href="{{ route('admin.absence.editAbsenceEmployeeDetail', ['id' => $list->id ]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
							<a href="" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="x_panel" style="overflow: auto;">
		<div class="row">
			<div class="col-md-6">
				<table class="table table-striped table-bordered">
					<tr>
						<th>Masuk Kerja</th>
						<td>{{ $masuk }}</td>
					</tr>
					<tr>
						<th>Tanggal Merah</th>
						<td>{{ $libur }}</td>
					</tr>
					<tr>
						<th>Sakit</th>
						<td>{{ $sakit }}</td>
					</tr>
					<tr>
						<th>Izin</th>
						<td>{{ $izin }}</td>
					</tr>
					<tr>
						<th>Cuti</th>
						<td>{{ $cuti }}</td>
					</tr>
					<tr>
						<th>Alpa</th>
						<td>{{ $alpa }}</td>
					</tr>
					<tr>
						<th>Gaji Diterima</th>
						<td>Rp. {{ number_format($total_gaji) }}</td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="table table-striped table-bordered">
					<tr>
						<th>Lembur</th>
						<td>Rp. {{ number_format($total_lembur) }}</td>
					</tr>
					<tr>
						<th>Uang Telat</th>
						<td>Rp. {{ number_format($total_telat) }}</td>
					</tr>
					<tr>
						<th>Uang Denda</th>
						<td>Rp. {{ number_format($total_denda) }}</td>
					</tr>
					<tr>
						<th>Gaji yang didapat</th>
						<td>Rp. {{ number_format($total_gaji + $total_lembur + $total_telat + $total_denda) }}</td>
					</tr>
				</table>
			</div>
		</div>
		
	</div>
	

@endsection