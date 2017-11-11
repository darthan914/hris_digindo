@extends('backend.layout.master')

@section('title')
	Laporan Absen - {{ $absenceEmployee->employee->name or $absenceEmployee->id_machine }}
@endsection

@section('script')
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<link href="{{ asset('backend/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('backend/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
<script type="text/javascript">
	$(function() {
		$('#datatable').DataTable({
			"columnDefs": [
				{ "orderable": true, "targets": 0 }
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

		$('#datatable').on('click', '.delete-employeeDetail', function(){
			$('.id-ondelete').val($(this).data('id'));
		});
	});
</script>
@endsection

@section('content')

	<div id="change-perday" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.absence.changePerday', ['id' => $absenceEmployee->id ]) }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Ganti Hari per Bulan</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="per_day" class="control-label col-md-3 col-sm-3 col-xs-12">Per Hari <span class="required">*</span>
							</label>
							<div class="col-md-9 col-sm-9 col-xs-12">
								<input type="text" class="form-control {{$errors->first('per_day') != '' ? 'parsley-error' : ''}}" name="per_day" value="{{ $absenceEmployee->per_day }}">
								<ul class="parsley-errors-list filled">
									<li class="parsley-required">{{ $errors->first('per_day') }}</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<button type="submit" class="btn btn-success">Submit</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{-- Delete Absence --}}
	<div id="delete-employeeDetail" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.absence.deleteAbsenceEmployeeDetail', ['id' => $absenceEmployee->id]) }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Delete Detail Absence?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id-ondelete" value="{{old('id')}}">
						<button type="submit" class="btn btn-danger">Delete</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>
				</form>
			</div>
		</div>
	</div>

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

		<table class="table table-striped table-bordered" id="datatable">
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
						<td>{{ date('D - d/m/Y', strtotime($list->date)) }}</td>
						<td>{{ date('H:i', strtotime($list->check_in)) }}</td>
						<td>{{ date('H:i', strtotime($list->check_out)) }}</td>
						<td>{{ $list->status }} : {{ $list->status_note or '' }}</td>
						<td>Rp. {{ number_format($list->gaji_pokok) }}</td>
						<td>Rp. {{ number_format($list->gaji_pokok / $present) }}</td>
						<td>
							{{ $list->gaji }}
							@php $total_gaji += ($list->gaji_pokok / $present) * $list->gaji; @endphp
						</td>
						
						<td>{{ $list->time_overtime >= '1900-01-01 00:00:00' ? date('d-m-Y H:i', strtotime($list->time_overtime)) : '-' }}</td>
						<td>
							@php $total_lembur += $list->point_overtime * $list->payment_overtime; @endphp
							Rp. {{ number_format($list->point_overtime * $list->payment_overtime) }} ({{ $list->point_overtime }})
						</td>
						<td>
							@php $total_telat += $list->point_late * $list->fine_late; @endphp
							Rp. {{ number_format($list->point_late * $list->fine_late) }} ({{$list->point_late}})
						</td>
						<td>
							@php $total_denda += $list->fine_additional; @endphp
							Rp. {{ number_format($list->fine_additional) }}
						</td>
						
						<td nowrap>
							<a href="{{ route('admin.absence.editAbsenceEmployeeDetail', ['id' => $list->id ]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
							<button class="btn btn-xs btn-danger delete-employeeDetail" data-toggle="modal" data-target="#delete-employeeDetail" data-id="{{ $list->id }}"><i class="fa fa-trash"></i></button>
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
						<th>Hari Per Bulan</th>
						<td>{{ $absenceEmployee->per_day }} <button class="btn btn-xs btn-primary change-perday" title="ganti hari per bulan" data-toggle="modal" data-target="#change-perday"><i class="fa fa-pencil"></i></button></td>
					</tr>
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
						<td>Rp. {{ number_format($total_gaji + $total_lembur + $total_denda - $total_telat) }}</td>
					</tr>
				</table>
			</div>
		</div>
		
	</div>
	

@endsection