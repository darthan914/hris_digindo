@extends('backend.layout.master')

@section('title')
	Dashboard
@endsection

@section('script')
<script type="text/javascript">
	function number_format (number, decimals, dec_point, thousands_sep) {
		// Strip all characters but numerical ones.
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		return s.join(dec);
	}

	Number.prototype.padLeft = function(base,chr){
	   var  len = (String(base || 10).length - String(this).length)+1;
	   return len > 0? new Array(len).join(chr || '0')+this : this;
	}

	function date_format(date)
	{
		if(date != null)
		{
			var d = new Date(date),
	        dformat = [ d.getDate().padLeft(), (d.getMonth()+1).padLeft(), d.getFullYear()].join('/');
	        return dformat;
		}
		else
		{
			return '';
		}
	}
	
	$(function() {
		$.ajax({
			url: "{{ route('admin.home.ajaxEmployee') }}",
			type: "POST",
			data: {
				f_year: $('*[name=f_year]').val(),
				f_month: $('*[name=f_month]').val(),
			},
		}).done(function(data) {
			$.each(data.birthday_today, function(index, list) {
				$("#birthday-today").append("<li>"+list.name+" ("+date_format(list.birthday)+")</li>");
			});
			
			$.each(data.birthday_monthly, function(index, list) {
				$("#birthday-monthly").append("<li>"+list.name+" ("+date_format(list.birthday)+")</li>");
			});

			$.each(data.end_contract_today, function(index, list) {
				$("#end-contract-today").append("<li>"+list.name+" ("+date_format(list.end_date_contract)+")</li>");
			});

			$.each(data.end_contract_monthly, function(index, list) {
				$("#end-contract-monthly").append("<li>"+list.name+" ("+date_format(list.end_date_contract)+")</li>");
			});
		});
	});
</script>
@endsection

@section('content')
	<div class="x_panel" style="overflow: auto;">
		<form class="form-inline" method="get">
			<select class="form-control" name="f_year" onchange="this.form.submit()">
				<option value="">This Year</option>
				{{-- <option value="all" {{ $request->f_year == 'all' ? 'selected' : '' }}>All Year</option> --}}
				@foreach($year as $list)
				<option value="{{ $list }}" {{ $request->f_year == $list ? 'selected' : '' }}>{{ $list }}</option>
				@endforeach
			</select>
			<select class="form-control" name="f_month" onchange="this.form.submit()">
				<option value="">This Month</option>
				{{-- <option value="all" {{ $request->f_month == 'all' ? 'selected' : '' }}>All Month</option> --}}
				@php $numMonth = 1; @endphp
				@foreach($month as $list)
				<option value="{{ $numMonth }}" {{ $request->f_month == $numMonth++ ? 'selected' : '' }}>{{ $list }}</option>
				@endforeach
			</select>
		</form>
	</div>
	<div class="x_panel" style="overflow: auto;">

		<h2>Ulang Tahun Hari Ini</h2>
		<ul id="birthday-today">
			
		</ul>
				
	</div>

	<div class="x_panel" style="overflow: auto;">

		<h2>Ulang Tahun Bulan Ini</h2>
		<ul id="birthday-monthly">
			
		</ul>
				
	</div>

	<div class="x_panel" style="overflow: auto;">

		<h2>Kontrak Habis Hari Ini</h2>
		<ul id="end-contract-today">
			
		</ul>
				
	</div>

	<div class="x_panel" style="overflow: auto;">

		<h2>Kontrak Habis Bulan Ini</h2>
		<ul id="end-contract-monthly">
			
		</ul>
				
	</div>
@endsection