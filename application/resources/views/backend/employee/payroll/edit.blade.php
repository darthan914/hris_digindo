@extends('backend.layout.master')

@section('title')
	Edit Arsip Gaji
@endsection

@section('script')
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

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

	    $('input[name="update_payroll"], input[name="date_change"]').daterangepicker({
	    	locale: {
		      format: 'DD MMMM YYYY'
		    },
	        singleDatePicker: true,
	        showDropdowns: true
	    });

	    $('select[name="id_job_title"]').select2();
	});
</script>
@endsection

@section('content')

	<h1>Edit Arsip Gaji</h1>
	<div class="row">

		<div class="col-md-12">
			<div class="x_panel">
				<h2>Gaji Karyawan</h2>
				<form class="form-horizontal form-label-left" action="{{ route('admin.employee.updatePayroll', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">

					<div class="form-group">
						<label for="gaji_pokok" class="control-label col-md-3 col-sm-3 col-xs-12">Gaji Pokok <span class="required">*</span>
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" id="gaji_pokok" name="gaji_pokok" class="form-control {{$errors->first('gaji_pokok') != '' ? 'parsley-error' : ''}}" value="{{ old('gaji_pokok',  $index->gaji_pokok)  }}">
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
						<label for="uang_telat" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Telat
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" id="uang_telat" name="uang_telat" class="form-control {{$errors->first('uang_telat') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_telat', $index->uang_telat)  }}">
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
						<label for="uang_lembur" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Lembur
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" id="uang_lembur" name="uang_lembur" class="form-control {{$errors->first('uang_lembur') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_lembur', $index->uang_lembur)  }}">
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

					<div class="form-group">
						<label for="update_payroll" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Mulai Gaji <span class="required">*</span>
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" id="update_payroll" name="update_payroll" class="form-control {{$errors->first('update_payroll') != '' ? 'parsley-error' : ''}}" value="{{ old('update_payroll', date('d F Y', strtotime($index->update_payroll))) }}">
							<ul class="parsley-errors-list filled">
								<li class="parsley-required">{{ $errors->first('update_payroll') }}</li>
							</ul>
						</div>
					</div>

					<div class="form-group">
						<label for="note" class="control-label col-md-3 col-sm-3 col-xs-12">Catatan <span class="required">*</span>
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<textarea id="note" name="note" class="form-control {{$errors->first('note') != '' ? 'parsley-error' : ''}}">{{ old('note', $index->note) }}</textarea>
							<ul class="parsley-errors-list filled">
								<li class="parsley-required">{{ $errors->first('note') }}</li>
							</ul>
						</div>
					</div>

					<div class="form-group">
						<label for="date_change" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Perubahan <span class="required">*</span>
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" id="date_change" name="date_change" class="form-control {{$errors->first('date_change') != '' ? 'parsley-error' : ''}}" value="{{ old('date_change', $index->date_change) }}">
							<ul class="parsley-errors-list filled">
								<li class="parsley-required">{{ $errors->first('date_change') }}</li>
							</ul>
						</div>
					</div>

					<div class="ln_solid"></div>

					<div class="form-group">
						<div class="col-md-9 col-sm-6 col-xs-12 col-md-offset-3">
							{{ csrf_field() }}
							<a class="btn btn-primary" href="{{ route('admin.employee') }}">Kembali</a>
							<button type="submit" class="btn btn-success">Ubah</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>


			
	

@endsection