@extends('backend.layout.master')

@section('title')
	Absen Karyawan - Buat
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script type="text/javascript">
	$(function() {
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

@section('content')

	<h1>Absen Karyawan - Buat</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.absence.storeEmployee', [$index->id]) }}" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="id_employee" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="id_employee" name="id_employee" class="form-control {{$errors->first('id_employee') != '' ? 'parsley-error' : ''}}" value="{{ old('id_employee') }}">
						<option value="">-- Pilih Karyawan --</option>
						@foreach($employee as $list)
						<option value="{{ $list->id }}" @if(old('id_employee') == $list->id) selected @endif>{{ $list->name }}</option>
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
					<input type="text" id="day_per_month" name="day_per_month" class="form-control {{$errors->first('day_per_month') != '' ? 'parsley-error' : ''}}" value="{{ old('day_per_month') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('day_per_month') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="gaji_pokok" class="control-label col-md-3 col-sm-3 col-xs-12">Gaji Pokok <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="gaji_pokok" name="gaji_pokok" class="form-control {{$errors->first('gaji_pokok') != '' ? 'parsley-error' : ''}}" value="{{ old('gaji_pokok') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('gaji_pokok') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="tunjangan" class="control-label col-md-3 col-sm-3 col-xs-12">Tunjangan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="tunjangan" name="tunjangan" class="form-control {{$errors->first('tunjangan') != '' ? 'parsley-error' : ''}}" value="{{ old('tunjangan') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('tunjangan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="perawatan_motor" class="control-label col-md-3 col-sm-3 col-xs-12">Perawatan Motor
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="perawatan_motor" name="perawatan_motor" class="form-control {{$errors->first('perawatan_motor') != '' ? 'parsley-error' : ''}}" value="{{ old('perawatan_motor') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('perawatan_motor') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_makan" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Makan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_makan" name="uang_makan" class="form-control {{$errors->first('uang_makan') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_makan') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_makan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="transport" class="control-label col-md-3 col-sm-3 col-xs-12">Transport
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="transport" name="transport" class="form-control {{$errors->first('transport') != '' ? 'parsley-error' : ''}}" value="{{ old('transport') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('transport') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="bpjs_kesehatan" class="control-label col-md-3 col-sm-3 col-xs-12">BPJS Kesehatan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="bpjs_kesehatan" name="bpjs_kesehatan" class="form-control {{$errors->first('bpjs_kesehatan') != '' ? 'parsley-error' : ''}}" value="{{ old('bpjs_kesehatan') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('bpjs_kesehatan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="bpjs_ketenagakerjaan" class="control-label col-md-3 col-sm-3 col-xs-12">BPJS Ketenagakerjaan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="bpjs_ketenagakerjaan" name="bpjs_ketenagakerjaan" class="form-control {{$errors->first('bpjs_ketenagakerjaan') != '' ? 'parsley-error' : ''}}" value="{{ old('bpjs_ketenagakerjaan') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('bpjs_ketenagakerjaan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_telat" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Telat <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_telat" name="uang_telat" class="form-control {{$errors->first('uang_telat') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_telat') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_telat') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_telat_permenit" class="control-label col-md-3 col-sm-3 col-xs-12">Kelipatan Uang Telat (Menit)<span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_telat_permenit" name="uang_telat_permenit" class="form-control {{$errors->first('uang_telat_permenit') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_telat_permenit') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_telat_permenit') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_lembur" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Lembur <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_lembur" name="uang_lembur" class="form-control {{$errors->first('uang_lembur') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_lembur') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_lembur') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_lembur_permenit" class="control-label col-md-3 col-sm-3 col-xs-12">Kelipatan Uang Lembur (Menit)<span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_lembur_permenit" name="uang_lembur_permenit" class="form-control {{$errors->first('uang_lembur_permenit') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_lembur_permenit') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_lembur_permenit') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="pph" class="control-label col-md-3 col-sm-3 col-xs-12">PPh
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="pph" name="pph" class="form-control {{$errors->first('pph') != '' ? 'parsley-error' : ''}}" value="{{ old('pph') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('pph') }}</li>
					</ul>
				</div>
			</div>


			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.absence.edit', [$index->id]) }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection