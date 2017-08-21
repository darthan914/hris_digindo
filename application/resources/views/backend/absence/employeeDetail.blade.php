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
					<!-- <a href="{{ route('admin.absence.create') }}" class="btn btn-default">Buat Baru</a> -->
					<!-- <select class="form-control" name="action">
						<option value="enable">Enable</option>
						<option value="disable">Disable</option>
						<option value="delete">Hapus</option>
					</select>
					<button type="submit" class="btn btn-success">Terapkan yang dipilih</button> -->
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
					<th>Tanggal</th>
					<th>Masuk</th>
					<th>Pulang</th>
					<th>Status</th>
					<th>Gaji</th>
					
					<th>Jam Lembur</th>
					<th>Bonus Lembur</th>
					<th>Telat</th>
					<th>Potongan Uang Telat</th>
					<!-- <th>Action</th> -->
				</tr>
			</thead>
			<tbody>
				@php $count=0; @endphp
				@php $payroll = $bonusOvertime = $minuteLate = $fineLate = $hadir = $libur = $sakit = $izin = $cuti = $alpa = 0; @endphp
				@foreach($date as $date)
					<tr>
						<td class="a-center ">
							<input type="checkbox" class="check" value="" name="id[]" form="action">
						</td>
						<td>{{ ++$count }}</td>
						<td>{{ date('l, d F Y', strtotime($date)) }}</td>
						<td>
							@php $time_in = $time_out = ''; @endphp
							@foreach($index as $list2)
								@if ($date == date('Y-m-d', strtotime($list2->time_in)))
									@php $time_in = $list2->time_in; @endphp
									@php $time_out = $list2->time_out; @endphp
									@break
								@endif
							@endforeach
							{{ $time_in ? date('H:i:s', strtotime($time_in)) : '' }}
						</td>
						<td>{{ $time_out ? date('H:i:s', strtotime($time_out)) : '' }}</td>
						<td>
							@php $day = $shift_in = $shift_out = ''; @endphp
							@foreach($shift as $list2)
								@if (date('w', strtotime($date)) == $list2->day)
									@php $day = $list2->day; @endphp
									@php $shift_in = $list2->shift_in; @endphp
									@php $shift_out = $list2->shift_out; @endphp
									@break
								@endif
							@endforeach

							@php $holidayName = $holidayDate = ''; @endphp
							@foreach($holiday as $list2)
								@if ($date == $list2->date)
									@php $holidayName = $list2->name; @endphp
									@php $holidayDate = $list2->date; @endphp
									@break
								@endif
							@endforeach

							@php $dayoffStart = $dayoffEnd = $dayoffNote = $dayoffType = ''; @endphp
							@foreach($dayoff as $list2)
								@if ($list2->start_dayoff <= $date && $date <= $list2->end_dayoff)
									@php $dayoffStart = $list2->start_dayoff; @endphp
									@php $dayoffEnd = $list2->end_dayoff; @endphp
									@php $dayoffNote = $list2->note; @endphp
									@php $dayoffType = $list2->type; @endphp
									@break
								@endif
							@endforeach

							@if($day && $time_in && $time_out)
								Hadir
								@php $gaji = 1; @endphp
								@if($holidayDate)
									<br/>Libur : {{ $holidayName }}
									@php $gaji = 1.5; @endphp
								@endif
								@php $hadir++; @endphp
							@elseif(!$day && $time_in && $time_out)
								Hadir
								@php $gaji = 1.5; @endphp
								@php $hadir++; @endphp
							@else
								@if($holidayDate)
									Libur : {{ $holidayName }}
									@if($day)
										@php $gaji = 1; @endphp
										@php $libur++; @endphp
									@endif
								@elseif($dayoffStart <= $date && $date <= $dayoffEnd)
									@if($list2->type == 'cuti')
										Cuti : {{ $dayoffNote }}
										@php $cuti++; @endphp
									@elseif($list2->type == 'izin')
										Izin : {{ $dayoffNote }}
										@php $izin++; @endphp
									@elseif($list2->type == 'sakit')
										Sakit : {{ $dayoffNote }}
										@php $sakit++; @endphp
									@endif
									
									@php $gaji = 1; @endphp
								@else
									@if(!$day)
										Kosong
										@php $gaji = 0; @endphp
									@else
										Alpa
										@php $gaji = -1; @endphp
										@php $alpa++; @endphp
									@endif
								@endif
							@endif
						</td>
						<td>
							{{ $gaji }}
							@php $payroll += $gaji @endphp
						</td>
						<td>
							@php $overtimeDate = $overtimeEnd = ''; @endphp
							@foreach($overtime as $list2)
								@if ($date == $list2->date)
									@php $overtimeDate = $list2->date; @endphp
									@php $overtimeEnd = $list2->end_overtime; @endphp
									@break
								@endif
							@endforeach
							{{ $overtimeEnd ? date('d F Y H:i:s', strtotime($overtimeEnd)) : '' }}
						</td>
						<td>
							@if($jobOvertime->book_overtime)
								@if($overtimeEnd)
									@php $lowOvertime = min(strtotime($time_out),strtotime($overtimeEnd)); @endphp
									
									@php $totalOvertime = $lowOvertime - strtotime($date.' '.$shift_out); @endphp
									@php $clockOvertime = (int)(($totalOvertime / 60) / 15)/4; @endphp
									@if($jobOvertime->min_overtime < (int)($totalOvertime / 60))
										@if($clockOvertime > 4)
											@php $sumOvertime = 4 + (($clockOvertime - 4) * 1.5); @endphp
										@else
											@php $sumOvertime = $clockOvertime; @endphp
										@endif
									@else
										@php $sumOvertime = 0; @endphp
									@endif
								@else
									@php $sumOvertime = 0; @endphp
								@endif
							@else
								@if($date.' '.$shift_out < $time_out)
									@php $totalOvertime = strtotime($time_out) - strtotime($date.' '.$shift_out); @endphp
									@if(!$day)
										@php $totalOvertime = strtotime($time_out) - strtotime($time_in); @endphp
									@endif
									@php $clockOvertime = (int)(($totalOvertime / 60) / 15)/4; @endphp

									@if($jobOvertime->min_overtime < $clockOvertime)
										@if($clockOvertime > 4)
											@php $sumOvertime = 4 * $gaji + (($clockOvertime - 4) * (1.5 + $gaji)); @endphp
										@else
											@php $sumOvertime = $clockOvertime; @endphp
										@endif
									@else
										@php $sumOvertime = 0; @endphp
									@endif
								@else
									@php $sumOvertime = 0; @endphp
								@endif
							@endif
							{{ $sumOvertime }}
							@php $bonusOvertime += $sumOvertime; @endphp
						</td>
						<td nowrap>
							@php $totalLate = 0; @endphp
							@if($day && $date.' '.$shift_in < $time_in)
								@php $totalLate = strtotime($time_in) - strtotime($date.' '.$shift_in); @endphp
								Telat : {{ (int)($totalLate / 60) }} Menit<br/>
								Denda : {{ 1 + (int)(($totalLate / 60)/15) }} x {{ $absenceEmployee->employee->uang_telat }}
							@endif
							@php $minuteLate += (int)($totalLate / 60) @endphp
						</td>
						<td>
							@if($day && $date.' '.$shift_in < $time_in)
								{{ (1 + (int)(($totalLate / 60)/15)) * $absenceEmployee->employee->uang_telat }}
								@php 
									$fineLate += (1 + (int)(($totalLate / 60)/15)) * $absenceEmployee->employee->uang_telat 
								@endphp
							@endif
						</td>
						
						<!-- <td nowrap>
							<a href="" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
							<a href="" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
						</td> -->
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
						<td>{{ $hadir }}</td>
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
						<th>Total Gaji Pokok</th>
						<td>{{ $payroll }}</td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="table table-striped table-bordered">
					<tr>
						<th>Lembur</th>
						<td>{{ $bonusOvertime }}</td>
					</tr>
					<tr>
						<th>Uang Telat</th>
						<td>{{ $fineLate }}</td>
					</tr>
				</table>
			</div>
		</div>
		
	</div>
	

@endsection