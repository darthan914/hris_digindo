@extends('backend.layout.master')

@section('title')
	Laporan Absen - {{ $absenceEmployee->employee->name or $absenceEmployee->id_machine }} - Buat
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('backend/vendors/timepicker/jquery.timepicker.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/timepicker/jquery.timepicker.min.css') }}" />

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

	function timeConverter(time)
	{
		var d = new Date(time);
		return ("0" + d.getHours()).slice(-2)+':'+("0" + d.getMinutes()).slice(-2);
	}

	$(function() {
	    $('input[name="date"]').daterangepicker({
	    	locale: {
		      format: 'DD MMMM YYYY'
		    },
	        singleDatePicker: true,
	        showDropdowns: true
	    });

	    $('input[name="time_overtime"]').daterangepicker({
	    	locale: {
		      format: 'DD MMMM YYYY HH:mm'
		    },
	        singleDatePicker: true,
	        timePicker24Hour: true,
	        timePicker: true,
	        showDropdowns: true
	    });

	    $('select[name="status"]').select2();

	    $('input[name="schedule_in"], input[name="schedule_out"], input[name="time_in"], input[name="time_out"]').timepicker({
		    timeFormat: 'H:mm',
		    interval: 1,
		    startTime: '5:00',
		    dynamic: false,
		    dropdown: true,
		    scrollbar: true
		});

	    $('input[name="date"]').change(function(event) {
	    	$.getJSON({url: "{{ route('admin.absence.getDataAbsenceEmployeeDetail') }}?id_absence_employee={{ $absenceEmployee->id }}&date="+$('input[name="date"]').val(), success: function(result){

					$('input[name=schedule_in]').val(result.shift.shift_in);
					$('input[name=schedule_out]').val(result.shift.shift_out);
					$('select[name=status] option[value='+result.status+']').prop('selected', true).trigger('change');
					$('textarea[name=status_note]').val(result.status_note);
					$('input[name=time_overtime]').val(result.time_overtime);
				}
			});
	    });
	});
</script>
@endsection

@section('content')

	<h1>Laporan Absen - {{ $absenceEmployee->employee->name or $absenceEmployee->id_machine }} - Buat</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.absence.storeAbsenceEmployeeDetail', ['id' => $absenceEmployee->id]) }}" method="post" enctype="multipart/form-data">
			{{ csrf_field() }}
			<div class="form-group">
				<label for="date" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="date" class="form-control {{$errors->first('date') != '' ? 'parsley-error' : ''}}" value="{{ old('date') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('date') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="schedule_in" class="control-label col-md-3 col-sm-3 col-xs-12">Jadwal Jam Masuk <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="schedule_in" name="schedule_in" class="form-control {{$errors->first('schedule_in') != '' ? 'parsley-error' : ''}}" value="{{ old('schedule_in') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('schedule_in') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="schedule_out" class="control-label col-md-3 col-sm-3 col-xs-12">Jadwal Jam Keluar <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="schedule_out" name="schedule_out" class="form-control {{$errors->first('schedule_out') != '' ? 'parsley-error' : ''}}" value="{{ old('schedule_out') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('schedule_out') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="time_in" class="control-label col-md-3 col-sm-3 col-xs-12">Absen Jam Masuk <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="time_in" name="time_in" class="form-control {{$errors->first('time_in') != '' ? 'parsley-error' : ''}}" value="{{ old('time_in') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('time_in') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="time_out" class="control-label col-md-3 col-sm-3 col-xs-12">Absen Jam Keluar <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="time_out" name="time_out" class="form-control {{$errors->first('time_out') != '' ? 'parsley-error' : ''}}" value="{{ old('time_out') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('time_out') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="status" class="control-label col-md-3 col-sm-3 col-xs-12">Status <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="status" name="status" class="form-control {{$errors->first('status') != '' ? 'parsley-error' : ''}}">
						<option value="kosong" @if(old('status') == 'kosong') selected @endif>Kosong</option>
						<option value="masuk" @if(old('status') == 'masuk') selected @endif>Masuk</option>
						<option value="libur" @if(old('status') == 'libur') selected @endif>Libur</option>
						<option value="cuti" @if(old('status') == 'cuti') selected @endif>Cuti</option>
						<option value="izin" @if(old('status') == 'izin') selected @endif>Izin</option>
						<option value="sakit" @if(old('status') == 'sakit') selected @endif>Sakit</option>
						<option value="alpa" @if(old('status') == 'alpa') selected @endif>Alpa</option>
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('status') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="status_note" class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan <span class="required">*(kecuali Kosong dan Masuk)</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea id="status_note" name="status_note" class="form-control {{$errors->first('status_note') != '' ? 'parsley-error' : ''}}">{{ old('status_note') }}</textarea>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('status_note') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="time_overtime" class="control-label col-md-3 col-sm-3 col-xs-12">Jam Lembur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="time_overtime" class="form-control {{$errors->first('time_overtime') != '' ? 'parsley-error' : ''}}" value="{{ old('time_overtime') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('time_overtime') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="point_overtime" class="control-label col-md-3 col-sm-3 col-xs-12">Point Lembur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="point_overtime" class="form-control {{$errors->first('point_overtime') != '' ? 'parsley-error' : ''}}" value="{{ old('point_overtime') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('point_overtime') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="payment_overtime" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Lembur
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="payment_overtime" class="form-control {{$errors->first('payment_overtime') != '' ? 'parsley-error' : ''}}" value="{{ old('payment_overtime', $absenceEmployee->employee->uang_lembur) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('payment_overtime') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="gaji_pokok" class="control-label col-md-3 col-sm-3 col-xs-12">Gaji Pokok <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="gaji_pokok" class="form-control {{$errors->first('gaji_pokok') != '' ? 'parsley-error' : ''}}" value="{{ old('gaji_pokok', $absenceEmployee->employee->gaji_pokok) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('gaji_pokok') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="fine_late" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Telat <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="fine_late" class="form-control {{$errors->first('fine_late') != '' ? 'parsley-error' : ''}}" value="{{ old('fine_late', $absenceEmployee->employee->uang_telat) }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('fine_late') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="fine_additional" class="control-label col-md-3 col-sm-3 col-xs-12">Uang Denda Tambahan
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="fine_additional" class="form-control {{$errors->first('fine_additional') != '' ? 'parsley-error' : ''}}" value="{{ old('fine_additional') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('fine_additional') }}</li>
					</ul>
				</div>
			</div>

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					<a class="btn btn-primary" href="{{ route('admin.absence.employeeDetail', ['id' => $absenceEmployee->id]) }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>

@endsection