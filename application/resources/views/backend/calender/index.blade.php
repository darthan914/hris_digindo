@extends('backend.layout.master')

@section('title')
	Kalender
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('backend/vendors/moment/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/vendors/fullcalendar/dist/fullcalendar.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('backend/vendors/fullcalendar/dist/fullcalendar.min.css') }}" />
<script src="{{ asset('backend/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script type="text/javascript">
	$(function() {
		$('#calendar-content').fullCalendar({
			events: [
				@foreach($dayoff as $list)
				{
					title: "{{ ucfirst($list->type) . ' : ' . $list->name}}",
					start: "{{ date('Y-m-d', strtotime($list->start_dayoff)) }}",
					@if($list->start_dayoff != $list->end_dayoff)
					end: "{{ date('Y-m-d', strtotime($list->end_dayoff. ' +1 day')) }}",
					@endif
					backgroundColor: '#ff6600',
					url: '{{ route('admin.dayoff.edit', ['id' => $list->id]) }}',
				},
				@endforeach
				@foreach($holiday as $list)
				{
					title: "{{ ucfirst($list->type) . ' : ' . $list->name}}",
					start: "{{ date('Y-m-d', strtotime($list->date)) }}",
					backgroundColor: '#990000',
					url: '{{ route('admin.holiday.edit', ['id' => $list->id]) }}',
				},
				@endforeach
			],
		});
	});
</script>
@endsection

@section('css')
<style type="text/css">
	.fc-sun { background: #ffa0a0 }
	.fc-sat { background: #d8fbff }
</style>
@endsection

@section('content')

	<h1>Kalender</h1>
	<div class="x_panel" style="overflow: auto;">
		<div id='calendar-content'></div>
	</div>

@endsection