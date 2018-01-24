@extends('backend.layout.master')

@section('title')
	Absen Karyawan
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

<script type="text/javascript">
	$(function() {
		var table = $('#datatable').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('admin.absence.datatablesEmployeeDetail', [$index->id]) }}",
				type: "post",
			},
			columns: [
				{data: 'check', orderable: false, searchable: false},

				{data: 'date'},

				{data: 'shift_in'},
				{data: 'shift_out'},
				{data: 'check_in'},
				{data: 'check_out'},

				{data: 'type_holiday', sClass: 'nowarp-cell'},
				{data: 'type_dayoff', sClass: 'nowarp-cell'},
				{data: 'end_overtime', sClass: 'nowarp-cell'},

				{data: 'minute_late', sClass: 'number-format'},
				{data: 'minute_overtime', sClass: 'number-format'},

				{data: 'point_lunch', sClass: 'number-format'},
				{data: 'point_alpa', sClass: 'number-format'},
				{data: 'point_pending', sClass: 'number-format'},
				{data: 'point_late', sClass: 'number-format'},
				{data: 'point_overtime', sClass: 'number-format'},

				{data: 'fine_additional', sClass: 'number-format'},

				{data: 'action', orderable: false, searchable: false, sClass: 'nowarp-cell'},
			],
			initComplete: function () {
				this.api().columns().every(function () {
					var column = this;
					var input = document.createElement("input");
					$(input).appendTo($(column.footer()).empty())
					.on('keyup', function () {
						column.search($(this).val(), false, false, true).draw();
					});
				});
			},
			scrollY: "400px",
			scrollX: true,
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

	    $('#datatable').on('click', '.delete-absenceEmployeeDetail', function(){
			$('.id_absence_employee_detail-ondelete').val($(this).data('id'));
		});

	    $('select[name="id_employee"]').select2();

	    $(".btn-pulldata").click(function(){
	    	if($('select[name=id_employee]').val() == '')
	    	{
	    		alert('pilih nama karyawan!');
	    	}
	    	else
	    	{
	    		$.post("{{ route('admin.absence.ajaxPayroll') }}",
		        {
		            id_employee: $('select[name=id_employee]').val(),
		        },
		        function(index){
		            $('input[name=day_per_month]').val(index.day_per_month);

		            $('input[name=gaji_pokok]').val(index.gaji_pokok);
		            $('input[name=tunjangan]').val(index.tunjangan);
		            $('input[name=perawatan_motor]').val(index.perawatan_motor);

		            $('input[name=uang_makan]').val(index.uang_makan);
		            $('input[name=transport]').val(index.transport);
		            $('input[name=bpjs_kesehatan]').val(index.bpjs_kesehatan);

		            $('input[name=bpjs_ketenagakerjaan]').val(index.bpjs_ketenagakerjaan);
		            $('input[name=uang_telat]').val(index.uang_telat);
		            $('input[name=uang_telat_permenit]').val(index.uang_telat_permenit);

		            $('input[name=uang_lembur]').val(index.uang_lembur);
		            $('input[name=uang_lembur_permenit]').val(index.uang_lembur_permenit);
		            $('input[name=pph]').val(index.pph);
		        });
	    	}
	        	
	    });
	});
</script>
@endsection

@section('css')
<link href="{{ asset('backend/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('backend/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css') }}" rel="stylesheet">
<style type="text/css">
	.nowarp-cell{
		white-space: nowrap;
	}
	.number-format{
		text-align: right;
		white-space: nowrap;
	}
</style>
@endsection

@section('content')

	@can('delete-absence')
	{{-- Delete Leave --}}
	<div id="delete-absenceEmployeeDetail" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="form-horizontal form-label-left" action="{{ route('admin.absence.deleteEmployeeDetail') }}" method="post" enctype="multipart/form-data">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Hapus Laporan Absen?</h4>
					</div>
					<div class="modal-body">
					</div>
					<div class="modal-footer">
						{{ csrf_field() }}
						<input type="hidden" name="id" class="id_absence_employee_detail-ondelete" value="{{old('id')}}">
						<button type="submit" class="btn btn-danger">Hapus</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@endcan

	<h1>Absen Karyawan</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.absence.updateEmployee', [$index->id]) }}" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="id_employee" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="id_employee" name="id_employee" class="form-control {{$errors->first('id_employee') != '' ? 'parsley-error' : ''}}" value="{{ old('id_employee') }}">
						<option value="">-- Pilih Karyawan --</option>
						@foreach($employee as $list)
						<option value="{{ $list->id }}" @if(old('id_employee', $index->id_employee) == $list->id) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('id_employee') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					<button type="button" class="btn btn-success btn-pulldata">Pull Data</button>
				</div>
			</div>

			<div class="form-group">
				<label for="day_per_month" class="control-label col-md-3 col-sm-3 col-xs-12">Gaji Per Bulan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="day_per_month" name="day_per_month" class="form-control {{$errors->first('day_per_month') != '' ? 'parsley-error' : ''}}" value="{{ old('day_per_month', $index->day_per_month) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('day_per_month') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="gaji_pokok" class="control-label col-md-3 col-sm-3 col-xs-12">Gaji Pokok <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="gaji_pokok" name="gaji_pokok" class="form-control {{$errors->first('gaji_pokok') != '' ? 'parsley-error' : ''}}" value="{{ old('gaji_pokok', $index->gaji_pokok) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('gaji_pokok') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="tunjangan" class="control-label col-md-3 col-sm-3 col-xs-12">Tunjangan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="tunjangan" name="tunjangan" class="form-control {{$errors->first('tunjangan') != '' ? 'parsley-error' : ''}}" value="{{ old('tunjangan', $index->tunjangan) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('tunjangan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="perawatan_motor" class="control-label col-md-3 col-sm-3 col-xs-12">Perawatan Motor
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="perawatan_motor" name="perawatan_motor" class="form-control {{$errors->first('perawatan_motor') != '' ? 'parsley-error' : ''}}" value="{{ old('perawatan_motor', $index->perawatan_motor) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('perawatan_motor') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_makan" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Makan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_makan" name="uang_makan" class="form-control {{$errors->first('uang_makan') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_makan', $index->uang_makan) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_makan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="transport" class="control-label col-md-3 col-sm-3 col-xs-12">Transport
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="transport" name="transport" class="form-control {{$errors->first('transport') != '' ? 'parsley-error' : ''}}" value="{{ old('transport', $index->transport) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('transport') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="bpjs_kesehatan" class="control-label col-md-3 col-sm-3 col-xs-12">BPJS Kesehatan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="bpjs_kesehatan" name="bpjs_kesehatan" class="form-control {{$errors->first('bpjs_kesehatan') != '' ? 'parsley-error' : ''}}" value="{{ old('bpjs_kesehatan', $index->bpjs_kesehatan) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('bpjs_kesehatan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="bpjs_ketenagakerjaan" class="control-label col-md-3 col-sm-3 col-xs-12">BPJS Ketenagakerjaan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="bpjs_ketenagakerjaan" name="bpjs_ketenagakerjaan" class="form-control {{$errors->first('bpjs_ketenagakerjaan') != '' ? 'parsley-error' : ''}}" value="{{ old('bpjs_ketenagakerjaan', $index->bpjs_ketenagakerjaan) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('bpjs_ketenagakerjaan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_telat" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Telat <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_telat" name="uang_telat" class="form-control {{$errors->first('uang_telat') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_telat', $index->uang_telat) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_telat') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_telat_permenit" class="control-label col-md-3 col-sm-3 col-xs-12">Kelipatan Uang Telat (Menit)<span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_telat_permenit" name="uang_telat_permenit" class="form-control {{$errors->first('uang_telat_permenit') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_telat_permenit', $index->uang_telat_permenit) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_telat_permenit') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_lembur" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Lembur <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_lembur" name="uang_lembur" class="form-control {{$errors->first('uang_lembur') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_lembur', $index->uang_lembur) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_lembur') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_lembur_permenit" class="control-label col-md-3 col-sm-3 col-xs-12">Kelipatan Uang Lembur (Menit)<span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_lembur_permenit" name="uang_lembur_permenit" class="form-control {{$errors->first('uang_lembur_permenit') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_lembur_permenit', $index->uang_lembur_permenit) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_lembur_permenit') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="pph" class="control-label col-md-3 col-sm-3 col-xs-12">PPh
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="pph" name="pph" class="form-control {{$errors->first('pph') != '' ? 'parsley-error' : ''}}" value="{{ old('pph', $index->pph) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('pph') }}</li>
					</ul>
				</div>
			</div>


			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.absence.edit', [$index->id_absence]) }}">Batal</a>
					@can('edit-absence')
					<button type="submit" class="btn btn-success">Submit</button>
					@endcan
				</div>
			</div>

		</form>
	</div>

	<div class="x_panel">
		<form method="post" id="action" action="{{ route('admin.absence.actionEmployeeDetail') }}" class="form-inline text-right" onsubmit="return confirm('Anda yakin untuk menerapkan yang dipilih')">
			@can('create-absence')
			<a href="{{ route('admin.absence.createEmployeeDetail', [$index->id]) }}" class="btn btn-default">Buat Baru</a>
			@endcan
			<select class="form-control" name="action">
				{{-- <option value="enable">Enable</option>
				<option value="disable">Disable</option> --}}
				<option value="delete">Hapus</option>
			</select>
			<button type="submit" class="btn btn-success">Terapkan yang dipilih</button>
		</form>

		<div class="ln_solid"></div>

		<table class="table table-striped table-bordered" id="datatable">
			<thead>
				<tr>
					<th width="100" nowrap>
						<label class="checkbox-inline"><input type="checkbox" data-target="check" class="check-all" id="check-all">Pilih Semua</label>
					</th>

					<th>Tanggal</th>

					<th>Shift In</th>
					<th>Shift Out</th>
					<th>Check In</th>
					<th>Check Out</th>

					<th>Libur</th>
					<th>Status</th>
					<th>Lembur</th>

					<th>Menit Telat</th>
					<th>Menit Lembur</th>

					<th>Poin Uang Makan</th>
					<th>Poin Alpa</th>
					<th>Poin Pending</th>
					<th>Poin Telat</th>
					<th>Poin Lembur</th>

					<th>Denda</th>

					<th>Aksi</th>
				</tr>
			</thead>

		</table>
	</div>

	<div class="x_panel">
		<div class="row">
			<div class="col-md-6">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Menit Telat</th>
							<td align="right">{{ number_format($index->minute_late) }}</td>
						</tr>
						<tr>
							<th>Menit Lembur</th>
							<td align="right">{{ number_format($index->minute_overtime) }}</td>
						</tr>
						<tr>
							<th>Poin Uang Makan</th>
							<td align="right">{{ number_format($index->point_lunch) }}</td>
						</tr>
						<tr>
							<th>Poin Alpa</th>
							<td align="right">{{ number_format($index->point_alpa) }}</td>
						</tr>
						<tr>
							<th>Poin Pending</th>
							<td align="right">{{ number_format($index->point_pending) }}</td>
						</tr>
						<tr>
							<th>Poin Telat</th>
							<td align="right">{{ number_format($index->point_late) }}</td>
						</tr>
						<tr>
							<th>Poin Lembur</th>
							<td align="right">{{ number_format($index->point_overtime) }}</td>
						</tr>
					</thead>

				</table>
			</div>
			<div class="col-md-6">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th></th>
							<th>Rumus</th>
							<th>Total</th>
						</tr>
						<tr>
							<th>Gaji Pokok</th>
							<td align="right" nowrap>{{ number_format($index->gaji_pokok) }}</td>
							<td align="right">{{ number_format($payroll = $index->gaji_pokok) }}</td>
							@php $payroll = ($index->gaji_pokok)  @endphp
						</tr>
						<tr>
							<th>Uang Makan</th>
							<td align="right" nowrap>({{ number_format($index->uang_makan) }} / {{ number_format($index->day_per_month) }}) x {{ number_format($index->point_lunch) }}</td>
							<td align="right">{{ number_format(($index->uang_makan / $index->day_per_month) * $index->point_lunch) }}</td>
							@php $payroll += (($index->uang_makan / $index->day_per_month) * $index->point_lunch) @endphp
						</tr>
						<tr>
							<th>Potongan Alpa</th>
							<td align="right" nowrap>
								({{ number_format($index->gaji_pokok) }} / {{ number_format($index->day_per_month) }}) x ({{ number_format($index->point_alpa) }} x 2)
							</td>
							<td align="right">
								{{ number_format(($index->gaji_pokok / $index->day_per_month) * ($index->point_alpa * 2),2) }}
							</td>
							@php $payroll -= ($index->gaji_pokok / $index->day_per_month) * ($index->point_alpa * 2)  @endphp
						</tr>
						<tr>
							<th>Potongan Pending</th>
							<td align="right" nowrap>
								({{ number_format($index->gaji_pokok) }} / {{ number_format($index->day_per_month) }}) x {{ number_format($index->point_pending) }}
							</td>
							<td align="right">
								{{ number_format(($index->gaji_pokok / $index->day_per_month) * $index->point_pending,2) }}
							</td>
							@php $payroll -= ($index->gaji_pokok / $index->day_per_month) * $index->point_pending  @endphp
						</tr>
						<tr>
							<th>Potongan Telat</th>
							<td align="right" nowrap>{{ number_format($index->uang_telat) }} x {{ number_format($index->point_late) }}</td>
							<td align="right">
								{{ number_format($index->uang_telat * $index->point_late) }}
							</td>
							@php $payroll -= ($index->uang_telat * $index->point_late)  @endphp
						</tr>
						<tr>
							<th>Potongan Denda</th>
							<td align="right" nowrap>{{ number_format($index->fine_additional) }}</td>
							<td align="right">{{ number_format($index->fine_additional) }}</td>
							@php $payroll -= ($index->fine_additional)  @endphp
						</tr>
						<tr>
							<th>Bonus Lembur</th>
							<td align="right" nowrap>{{ number_format($index->uang_lembur) }} x {{ number_format($index->point_overtime) }}</td>
							<td align="right">{{ number_format($index->uang_lembur * $index->point_overtime) }}</td>
							@php $payroll += ($index->uang_lembur * $index->point_overtime)  @endphp
						</tr>
						<tr>
							<th>Gaji yang didapat</th>
							<td align="right" nowrap></td>
							<td align="right">{{ number_format($payroll) }}</td>
						</tr>
					</thead>

				</table>
			</div>
		</div>
				
	</div>
	

@endsection