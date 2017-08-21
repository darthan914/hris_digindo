@extends('backend.layout.master')

@section('title')
	Cuti
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/fullcalendar/dist/fullcalendar.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/fullcalendar/dist/fullcalendar.min.css') }}" />
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script type="text/javascript">
	$(function() {
		$('#datatable-buttons, #datatable-buttons2').DataTable({
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

	<h1>Cuti</h1>
	<div class="x_panel" style="overflow: auto;">
		<form method="post" id="action" action="{{ route('admin.dayoff.action') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
			<a href="{{ route('admin.dayoff.create') }}" class="btn btn-default">Buat Baru</a>
			<select class="form-control" name="action">
				<option value="delete">Hapus</option>
			</select>
			<button type="submit" class="btn btn-success">Terapkan yang dipilih</button>
		</form>

		<ul class="nav nav-tabs">
			<li @if($tab == 'index') class="active" @endif><a data-toggle="tab" href="#index">Semua</a></li>
			<li @if($tab == 'leftDayoff') class="active" @endif><a data-toggle="tab" href="#leftDayoff">Sisa cuti karyawan</a></li>
		</ul>

		<div class="tab-content">
			<div id="index" class="tab-pane fade @if($tab == 'index') in active @endif">
				<form method="get" id="filter-index" class="form-inline text-right">
					<select name="f_id_employee" class="form-control" onchange="this.form.submit()">
						<option value="">-- Filter Karyawan --</option>
						@foreach($employee as $list)
						<option value="{{ $list->id }}" @if($f_id_employee == $list->id) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
					<select name="f_year" class="form-control" onchange="this.form.submit()">
						<option value="">-- Filter Tahun --</option>
						@foreach($year as $list)
						<option value="{{ $list->year }}" @if($f_year == $list->year) selected @endif>{{ $list->year }}</option>
						@endforeach
					</select>
					<input type="hidden" name="tab" value="index">
				</form>
				<table class="table table-striped table-bordered" id="datatable-buttons">
					<thead>
						<tr>
							<th width="100" nowrap>
								<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
							</th>
							<th>No.</th>
							<th>Nama</th>
							<th>Posisi</th>
							<th>Shift</th>
							<th>Tanggal Permintaan</th>
							<th>Total Cuti</th>
							<th>Dari Tanggal</th>
							<th>Ke Tanggal</th>
							<th>Tipe</th>
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
							<td>{{ $list->name }}</td>
							<td>{{ $list->position }}</td>
							<td><a href="{{ route('admin.shift.edit', ['id' => $list->id_shift]) }}">{{ $list->shift }}</a></td>
							<td>{{ date('d F Y', strtotime($list->date)) }}</td>
							<td>{{ $list->total_dayoff }}</td>
							<td>{{ date('d F Y', strtotime($list->start_dayoff)) }}</td>
							<td>{{ date('d F Y', strtotime($list->end_dayoff)) }}</td>
							<td>{{ $list->type }}</td>
							<td>{{ $list->note }}</td>
							<td nowrap>
								<a href="{{ route('admin.dayoff.edit', ['id' => $list->id]) }}" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>
								<a href="{{ route('admin.dayoff.delete', ['id' => $list->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Hapus Data?')"><i class="fa fa-trash"></i></a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div id="leftDayoff" class="tab-pane fade @if($tab == 'leftDayoff') in active @endif">
				<form method="get" id="filter-index" class="form-inline text-right">
					<select name="f_year" class="form-control" onchange="this.form.submit()">
						<option value="">-- Filter Tahun --</option>
						@foreach($year as $list)
						<option value="{{ $list->year }}" @if($f_year == $list->year) selected @endif>{{ $list->year }}</option>
						@endforeach
					</select>
					<input type="hidden" name="tab" value="leftDayoff">
				</form>
				<p>Total Cuti Bersama {{ $f_year ? $f_year : date('Y') }} : {{ $totalDayoff->first()->total_holiday }}</p>
				<table class="table table-striped table-bordered" id="datatable-buttons2">
					<thead>
						<tr>
							<th width="100" nowrap>
								<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
							</th>
							<th>No.</th>
							<th>Nama</th>
							<th>Total Cuti</th>
							<th>Libur Cuti</th>
							<!-- <th>Terakhir total Cuti</th> -->
							<!-- <th>Terakhir Libur Cuti</th> -->
							<th>Utang Cuti</th>
							<th>Sisa Cuti</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@php $count=0; @endphp
						@foreach($totalDayoff as $list)
						<tr>
							<td class="a-center ">
								<input type="checkbox" class="check" value="{{ $list->id }}" name="id[]" form="action">
							</td>
							<td>{{ ++$count }}</td>
							<td>{{ $list->name }}</td>
							<td>{{ $col1 = $list->dayoff()->whereYear('start_dayoff', $f_year ? $f_year : date('Y'))->sum('total_dayoff') }}</td>
							<td>{{ $holidayDayoff }}</td>
							<!-- <td>{{ $col2 = $list->dayoff()->whereYear('start_dayoff', $f_year ? $f_year-1 : date('Y')-1)->sum('total_dayoff') }}</td> -->
							<!-- <td>{{ $lastHolidayDayoff }}</td> -->
							<td>{{ $col3 = (12 - $col2 - $lastHolidayDayoff) < 0 ? (12 - $col2 - $lastHolidayDayoff) : 0 }}</td>
							<td>{{ 12 - $col1 - $holidayDayoff + $col3 }}</td>
							<td nowrap>
								<a href="{{ route('admin.dayoff', ['f_id_employee' => $list->id, 'f_year' => $f_year ? $f_year : date('Y')]) }}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	

@endsection