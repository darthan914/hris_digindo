@extends('backend.layout.master')

@section('title')
	Data Karyawan - Buat
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('backend/vendors/ckeditor/ckeditor.js') }}"></script>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.

    function autoUrl(from, to)
	{

		temp = document.getElementById(from).value;
		temp = temp.toLowerCase();
		temp = temp.replace(/ /g, "-");
		temp = encodeURI(temp);

		if(temp != '')
		{
			document.getElementById(to).value = temp;
		}
	}

	$(function() {
		var selector = 'input[name="date_join"], input[name="birthday"], input[name="start_date_contract"], input[name="end_date_contract"], input[name="update_payroll"], input[name=date_resign]';
	    $(selector).daterangepicker({
		    autoUpdateInput: false,
		    locale: {
		        cancelLabel: 'Clear'
		    },
		    singleDatePicker: true
		});

		$(selector).on('apply.daterangepicker', function(ev, picker) {
		    $(this).val(picker.startDate.format('DD MMMM YYYY'));
		});

	    $('select[name="leader"], select[name="id_shift"]').select2();
	});
</script>
@endsection

@section('content')

	<h1>Data Karyawan - Buat</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.employee.store') }}" method="post" enctype="multipart/form-data">
			<h2>Biodata Karyawan</h2>

			<div class="form-group">
				<label for="name" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="name" class="form-control {{$errors->first('name') != '' ? 'parsley-error' : ''}}" value="{{ old('name') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('name') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="birthday" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Lahir <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="birthday" name="birthday" class="form-control {{$errors->first('birthday') != '' ? 'parsley-error' : ''}}" value="{{ old('birthday') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('birthday') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="gender" class="control-label col-md-3 col-sm-3 col-xs-12">Jenis Kelamin <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="radio-inline"><input type="radio" id="gender-male" name="gender" value="male" @if(old('gender') != '' && old('gender') == 'male') checked @endif>Laki-laki</label> 
					<label class="radio-inline"><input type="radio" id="gender-female" name="gender" value="female" @if(old('gender') != '' && old('gender') == 'female') checked @endif>Perempuan</label>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('gender') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="religion" class="control-label col-md-3 col-sm-3 col-xs-12">Agama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="radio-inline"><input type="radio" id="religion-islam" name="religion" value="islam" @if(old('religion') != '' && old('religion') == 'islam') checked @endif>Islam</label> 
					<label class="radio-inline"><input type="radio" id="religion-kristen" name="religion" value="kristen" @if(old('religion') != '' && old('religion') == 'kristen') checked @endif>Kristen</label> 
					<label class="radio-inline"><input type="radio" id="religion-khatolik" name="religion" value="khatolik" @if(old('religion') != '' && old('religion') == 'khatolik') checked @endif>Khatolik</label> 
					<label class="radio-inline"><input type="radio" id="religion-buddha" name="religion" value="buddha" @if(old('religion') != '' && old('religion') == 'buddha') checked @endif>Buddha</label> 
					<label class="radio-inline"><input type="radio" id="religion-hindu" name="religion" value="hindu" @if(old('religion') != '' && old('religion') == 'hindu') checked @endif>Hindu</label> 
					<label class="radio-inline"><input type="radio" id="religion-other" name="religion" value="other" @if(old('religion') != '' && old('religion') == 'other') checked @endif>Other</label>
					
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('religion') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="no_ktp" class="control-label col-md-3 col-sm-3 col-xs-12">No KTP <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="no_ktp" name="no_ktp" class="form-control {{$errors->first('no_ktp') != '' ? 'parsley-error' : ''}}" value="{{ old('no_ktp') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('no_ktp') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="status" class="control-label col-md-3 col-sm-3 col-xs-12">Status <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="radio-inline"><input type="radio" id="status-single" name="status" value="single" @if(old('status') != '' && old('status') == 'single') checked @endif>Single</label> 
					<label class="radio-inline"><input type="radio" id="status-married" name="status" value="married" @if(old('status') != '' && old('status') == 'married') checked @endif>Menikah</label> 
					
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('status') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="ktp_address" class="control-label col-md-3 col-sm-3 col-xs-12">Alamat KTP <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea id="ktp_address" name="ktp_address" class="form-control {{$errors->first('ktp_address') != '' ? 'parsley-error' : ''}}">{{ old('ktp_address') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('ktp_address') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="current_address" class="control-label col-md-3 col-sm-3 col-xs-12">Alamat Sekarang
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea id="current_address" name="current_address" class="form-control {{$errors->first('current_address') != '' ? 'parsley-error' : ''}}">{{ old('current_address') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('current_address') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="npwp" class="control-label col-md-3 col-sm-3 col-xs-12">NPWP
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="npwp" name="npwp" class="form-control {{$errors->first('npwp') != '' ? 'parsley-error' : ''}}" value="{{ old('npwp') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('npwp') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="npwp_address" class="control-label col-md-3 col-sm-3 col-xs-12">Alamat NPWP
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea type="text" id="npwp_address" name="npwp_address" class="form-control {{$errors->first('npwp_address') != '' ? 'parsley-error' : ''}}">{{ old('npwp_address') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('npwp_address') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="npwp_status" class="control-label col-md-3 col-sm-3 col-xs-12">Status NPWP
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="npwp_status" name="npwp_status" class="form-control {{$errors->first('npwp_status') != '' ? 'parsley-error' : ''}}" value="{{ old('npwp_status') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('npwp_status') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="phone" class="control-label col-md-3 col-sm-3 col-xs-12">Nomor Telepon <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea id="phone" name="phone" class="form-control {{$errors->first('phone') != '' ? 'parsley-error' : ''}}">{{ old('phone') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('phone') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			
			<h2>Data Karyawan</h2>

			<div class="form-group">
				<label for="date_join" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Bergabung <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="date_join" name="date_join" class="form-control {{$errors->first('date_join') != '' ? 'parsley-error' : ''}}" value="{{ old('date_join') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('date_join') }}</li>
					</ul>
				</div>
			</div>
			
			<div class="form-group">
				<label for="nik" class="control-label col-md-3 col-sm-3 col-xs-12">NIK <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="nik" name="nik" class="form-control {{$errors->first('nik') != '' ? 'parsley-error' : ''}}" value="{{ old('nik') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('nik') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="job_title" class="control-label col-md-3 col-sm-3 col-xs-12">Posisi <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="job_title" name="job_title" class="form-control {{$errors->first('job_title') != '' ? 'parsley-error' : ''}}" value="{{ old('job_title') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('job_title') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="division" class="control-label col-md-3 col-sm-3 col-xs-12">Divisi
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="division" name="division" class="form-control {{$errors->first('division') != '' ? 'parsley-error' : ''}}" value="{{ old('division') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('division') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="sub_division" class="control-label col-md-3 col-sm-3 col-xs-12">Sub Divisi
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="sub_division" name="sub_division" class="form-control {{$errors->first('sub_division') != '' ? 'parsley-error' : ''}}" value="{{ old('sub_division') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('sub_division') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="level" class="control-label col-md-3 col-sm-3 col-xs-12">Sebagai <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="radio-inline"><input type="radio" name="level" value="staff" @if(old('level') == 'staff') checked @endif>Karyawan</label> 
					<label class="radio-inline"><input type="radio" name="level" value="leader" @if(old('level') == 'leader') checked @endif>Atasan</label> 
					
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('level') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="id_leader" class="control-label col-md-3 col-sm-3 col-xs-12">Atasan (Bila sebagai kepala karyawan tidak perlu dipilh)
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="id_leader" name="id_leader" class="form-control {{$errors->first('id_leader') != '' ? 'parsley-error' : ''}}">
						<option value="0">-- Pilih Atasan --</option>
						@foreach($headEmployee as $list)
						<option value="{{ $list->id }}" @if(old('id_leader') == $list->id) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('id_leader') }}</li>
					</ul>
				</div>
			</div>
			
			<div class="form-group">
				<label for="id_absence_machine" class="control-label col-md-3 col-sm-3 col-xs-12">No ID Absen
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="id_absence_machine" name="id_absence_machine" class="form-control {{$errors->first('id_absence_machine') != '' ? 'parsley-error' : ''}}" value="{{ old('id_absence_machine') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('id_absence_machine') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			
			<h2>Kontrak Karyawan</h2>

			<div class="form-group">
				<label for="type_contract" class="control-label col-md-3 col-sm-3 col-xs-12">Tipe Kontrak <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="radio-inline"><input type="radio" id="type_contract-contract" name="type_contract" value="contract" @if(old('type_contract') != '' && old('type_contract') == 'contract') checked @endif>
					Kontrak</label> 

					<label class="radio-inline"><input type="radio" id="type_contract-part-time" name="type_contract" value="part-time" @if(old('type_contract') != '' && old('type_contract') == 'part-time') checked @endif>
					Part-Time</label> 

					<label class="radio-inline"><input type="radio" id="type_contract-permanent" name="type_contract" value="permanent" @if(old('type_contract') != '' && old('type_contract') == 'permanent') checked @endif>
					Permanent</label> 
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('type_contract') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="start_date_contract" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Mulai Kontrak <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="start_date_contract" name="start_date_contract" class="form-control {{$errors->first('start_date_contract') != '' ? 'parsley-error' : ''}}" value="{{ old('start_date_contract') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('start_date_contract') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="end_date_contract" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Akhir Kontrak <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="end_date_contract" name="end_date_contract" class="form-control {{$errors->first('end_date_contract') != '' ? 'parsley-error' : ''}}" value="{{ old('end_date_contract') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('end_date_contract') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="id_shift" class="control-label col-md-3 col-sm-3 col-xs-12">Shift
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="id_shift" name="id_shift" class="form-control {{$errors->first('id_shift') != '' ? 'parsley-error' : ''}}">
						<option value="0">-- Pilih Shift --</option>
						@foreach($shift as $list)
						<option value="{{ $list->id }}" @if(old('id_shift')== $list->id) selected @endif>{{ $list->name }}</option>
						@endforeach
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('id_shift') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="need_book_overtime" class="control-label col-md-3 col-sm-3 col-xs-12">Butuh Surat Lembur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline"><input type="checkbox" id="need_book_overtime" name="need_book_overtime" value="1" @if(old('need_book_overtime') == '1') checked @endif>Ya</label> 
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('need_book_overtime') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="min_overtime" class="control-label col-md-3 col-sm-3 col-xs-12">Minimum Lembur (Per Menit)
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="min_overtime" name="min_overtime" class="form-control {{$errors->first('min_overtime') != '' ? 'parsley-error' : ''}}" value="{{ old('min_overtime', 15) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('min_overtime') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="guarantee" class="control-label col-md-3 col-sm-3 col-xs-12">Jaminan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="guarantee" name="guarantee" class="form-control {{$errors->first('guarantee') != '' ? 'parsley-error' : ''}}" value="{{ old('guarantee') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('guarantee') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="status_guarantee" class="control-label col-md-3 col-sm-3 col-xs-12">Status Jaminan <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline"><input type="checkbox" id="status_guarantee" name="status_guarantee" value="1" @if(old('status_guarantee') == '1') checked @endif>Disimpan</label> 

					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('status_guarantee') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			
			<h2>Gaji Karyawan</h2>

			<div class="form-group">
				<label for="gaji_pokok" class="control-label col-md-3 col-sm-3 col-xs-12">Gaji Pokok <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="gaji_pokok" name="gaji_pokok" class="form-control {{$errors->first('gaji_pokok') != '' ? 'parsley-error' : ''}}" value="{{ old('gaji_pokok',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('gaji_pokok') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="tunjangan" class="control-label col-md-3 col-sm-3 col-xs-12">Tunjangan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="tunjangan" name="tunjangan" class="form-control {{$errors->first('tunjangan') != '' ? 'parsley-error' : ''}}" value="{{ old('tunjangan',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('tunjangan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="perawatan_motor" class="control-label col-md-3 col-sm-3 col-xs-12">Perawatan Motor
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="perawatan_motor" name="perawatan_motor" class="form-control {{$errors->first('perawatan_motor') != '' ? 'parsley-error' : ''}}" value="{{ old('perawatan_motor',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('perawatan_motor') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_makan" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Makan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_makan" name="uang_makan" class="form-control {{$errors->first('uang_makan') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_makan',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_makan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="transport" class="control-label col-md-3 col-sm-3 col-xs-12">Transport
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="transport" name="transport" class="form-control {{$errors->first('transport') != '' ? 'parsley-error' : ''}}" value="{{ old('transport',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('transport') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="bpjs_kesehatan" class="control-label col-md-3 col-sm-3 col-xs-12">BPJS Kesehatan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="bpjs_kesehatan" name="bpjs_kesehatan" class="form-control {{$errors->first('bpjs_kesehatan') != '' ? 'parsley-error' : ''}}" value="{{ old('bpjs_kesehatan',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('bpjs_kesehatan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="bpjs_ketenagakerjaan" class="control-label col-md-3 col-sm-3 col-xs-12">BPJS Ketenagakerjaan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="bpjs_ketenagakerjaan" name="bpjs_ketenagakerjaan" class="form-control {{$errors->first('bpjs_ketenagakerjaan') != '' ? 'parsley-error' : ''}}" value="{{ old('bpjs_ketenagakerjaan',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('bpjs_ketenagakerjaan') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_telat" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Telat <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_telat" name="uang_telat" class="form-control {{$errors->first('uang_telat') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_telat',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_telat') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_telat_permenit" class="control-label col-md-3 col-sm-3 col-xs-12">Kelipatan Uang Telat (Menit)<span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_telat_permenit" name="uang_telat_permenit" class="form-control {{$errors->first('uang_telat_permenit') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_telat_permenit',15) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_telat_permenit') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_lembur" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Lembur <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_lembur" name="uang_lembur" class="form-control {{$errors->first('uang_lembur') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_lembur',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_lembur') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="uang_lembur_permenit" class="control-label col-md-3 col-sm-3 col-xs-12">Kelipatan Uang Lembur (Menit)<span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="uang_lembur_permenit" name="uang_lembur_permenit" class="form-control {{$errors->first('uang_lembur_permenit') != '' ? 'parsley-error' : ''}}" value="{{ old('uang_lembur_permenit',15) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('uang_lembur_permenit') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="pph" class="control-label col-md-3 col-sm-3 col-xs-12">PPh
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="pph" name="pph" class="form-control {{$errors->first('pph') != '' ? 'parsley-error' : ''}}" value="{{ old('pph',0) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('pph') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="update_payroll" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Update <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="update_payroll" name="update_payroll" class="form-control {{$errors->first('update_payroll') != '' ? 'parsley-error' : ''}}" value="{{ old('update_payroll') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('update_payroll') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			
			<h2>Hasil Test Karyawan</h2>

			<div class="form-group">
				<label for="test_disc" class="control-label col-md-3 col-sm-3 col-xs-12">Test Disc
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="test_disc" name="test_disc" class="form-control {{$errors->first('test_disc') != '' ? 'parsley-error' : ''}}" value="{{ old('test_disc') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('test_disc') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="test_gratyo" class="control-label col-md-3 col-sm-3 col-xs-12">Test Gratyo
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="test_gratyo" name="test_gratyo" class="form-control {{$errors->first('test_gratyo') != '' ? 'parsley-error' : ''}}" value="{{ old('test_gratyo') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('test_gratyo') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="test_math" class="control-label col-md-3 col-sm-3 col-xs-12">Test Math
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="test_math" name="test_math" class="form-control {{$errors->first('test_math') != '' ? 'parsley-error' : ''}}" value="{{ old('test_math') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('test_math') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			
			<h2>Emergency</h2>

			<div class="form-group">
				<label for="emergency_name" class="control-label col-md-3 col-sm-3 col-xs-12">Nama
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="emergency_name" name="emergency_name" class="form-control {{$errors->first('emergency_name') != '' ? 'parsley-error' : ''}}" value="{{ old('emergency_name') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('emergency_name') }}</li>
					</ul>
				</div>
			</div>
			
			<div class="form-group">
				<label for="emergency_phone" class="control-label col-md-3 col-sm-3 col-xs-12">Telepon
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="emergency_phone" name="emergency_phone" class="form-control {{$errors->first('emergency_phone') != '' ? 'parsley-error' : ''}}" value="{{ old('emergency_phone') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('emergency_phone') }}</li>
					</ul>
				</div>
			</div>
			
			<div class="form-group">
				<label for="emergency_relation" class="control-label col-md-3 col-sm-3 col-xs-12">Hubungan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="emergency_relation" name="emergency_relation" class="form-control {{$errors->first('emergency_relation') != '' ? 'parsley-error' : ''}}" value="{{ old('emergency_relation') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('emergency_relation') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			
			<h2>Resign</h2>
			
			<div class="form-group">
				<label for="status_resign" class="control-label col-md-3 col-sm-3 col-xs-12">Status Resign
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<label class="checkbox-inline"><input type="checkbox" id="status_resign" name="status_resign" value="1" @if(old('status_resign') == '1') checked @endif>Resign</label> 
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('status_resign') }}</li>
					</ul>
				</div>
			</div>
			
			<div class="form-group">
				<label for="date_resign" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Resign
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="date_resign" name="date_resign" class="form-control {{$errors->first('date_resign') != '' ? 'parsley-error' : ''}}" value="{{ old('date_resign') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('date_resign') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>

			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.employee') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit & Lihat</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection