@extends('backend.layout.master')

@section('title')
	Edit Arsip Kontrak
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

	    $(' input[name="start_date_contract"], input[name="end_date_contract"], input[name="date_change"]').daterangepicker({
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

	<h1>Edit Arsip Kontrak</h1>
	<div class="row">

		<div class="col-md-12">
			<div class="x_panel">
				<h2>Karyawan Kontrak</h2>
				<form class="form-horizontal form-label-left" action="{{ route('admin.employee.updateContract', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">

					<div class="form-group">
						<label for="type_contract" class="control-label col-md-3 col-sm-3 col-xs-12">Tipe Kontrak <span class="required">*</span>
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<label class="radio-inline"><input type="radio" id="type_contract-contract" name="type_contract" value="contract" @if(old('type_contract', $index->type_contract) == 'contract') checked @endif>
							Kontrak</label> 

							<label class="radio-inline"><input type="radio" id="type_contract-part-time" name="type_contract" value="part-time" @if(old('type_contract', $index->type_contract) == 'part-time') checked @endif>
							Part-Time</label> 

							<label class="radio-inline"><input type="radio" id="type_contract-permanent" name="type_contract" value="permanent" @if(old('type_contract', $index->type_contract) == 'permanent') checked @endif>
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
							<input type="text" id="start_date_contract" name="start_date_contract" class="form-control {{$errors->first('start_date_contract') != '' ? 'parsley-error' : ''}}" value="{{ old('start_date_contract', date('d F Y', strtotime($index->start_date_contract))) }}">
							<ul class="parsley-errors-list filled">
								<li class="parsley-required">{{ $errors->first('start_date_contract') }}</li>
							</ul>
						</div>
					</div>

					<div class="form-group">
						<label for="end_date_contract" class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Akhir Kontrak <span class="required">*</span>
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" id="end_date_contract" name="end_date_contract" class="form-control {{$errors->first('end_date_contract') != '' ? 'parsley-error' : ''}}" value="{{ old('end_date_contract', $index->end_date_contract) }}">
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
								<option value="{{ $list->id }}" @if(old('id_shift', $index->id_shift)== $list->id) selected @endif>{{ $list->name }}</option>
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
							<label class="checkbox-inline"><input type="checkbox" id="need_book_overtime" name="need_book_overtime" value="1" @if(old('need_book_overtime', $index->need_book_overtime) == '1') checked @endif>Ya</label> 
							<ul class="parsley-errors-list filled">
								<li class="parsley-required">{{ $errors->first('need_book_overtime') }}</li>
							</ul>
						</div>
					</div>

					<div class="form-group">
						<label for="min_overtime" class="control-label col-md-3 col-sm-3 col-xs-12">Minimum Lembur (Per Menit)
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" id="min_overtime" name="min_overtime" class="form-control {{$errors->first('min_overtime') != '' ? 'parsley-error' : ''}}" value="{{ old('min_overtime', $index->min_overtime) }}">
							<ul class="parsley-errors-list filled">
								<li class="parsley-required">{{ $errors->first('min_overtime') }}</li>
							</ul>
						</div>
					</div>

					<div class="form-group">
						<label for="guarantee" class="control-label col-md-3 col-sm-3 col-xs-12">Jaminan
						</label>
						<div class="col-md-9 col-sm-9 col-xs-12">
							<input type="text" id="guarantee" name="guarantee" class="form-control {{$errors->first('guarantee') != '' ? 'parsley-error' : ''}}" value="{{ old('guarantee', $index->guarantee) }}">
							<ul class="parsley-errors-list filled">
								<li class="parsley-required">{{ $errors->first('guarantee') }}</li>
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