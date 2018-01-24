@extends('backend.layout.master')

@section('title')
	User Management - Edit
@endsection

@section('script')
<script type="text/javascript">
	$(function() {
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

	<h1>User Management - Edit</h1>
	<div class="x_panel">
		<form class="form-horizontal form-label-left" action="{{ route('admin.user.update', ['id' => $index->id]) }}" method="post" enctype="multipart/form-data">

			<div class="form-group">
				<label for="name" class="control-label col-md-3 col-sm-3 col-xs-12">Nama <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="for" name="name" class="form-control {{$errors->first('name') != '' ? 'parsley-error' : ''}}" value="{{ old('name') == '' ? $index->name : old('name') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('name') }}</li>
					</ul>
				</div>
			</div>
			
			<div class="form-group">
				<label for="username" class="control-label col-md-3 col-sm-3 col-xs-12">Username <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="text" id="username" name="username" class="form-control {{$errors->first('username') != '' ? 'parsley-error' : ''}}" value="{{ old('username') == '' ? $index->username : old('username') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('username') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="email" class="control-label col-md-3 col-sm-3 col-xs-12">Email <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="email" id="email" name="email" class="form-control {{$errors->first('email') != '' ? 'parsley-error' : ''}}" value="{{ old('email') == '' ? $index->email : old('email') }}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('email') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="password" class="control-label col-md-3 col-sm-3 col-xs-12">Password</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="password" id="password" name="password" class="form-control {{$errors->first('password') != '' ? 'parsley-error' : ''}}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('password') }}</li>
					</ul>
				</div>
			</div>

			<div class="form-group">
				<label for="password_confirmation" class="control-label col-md-3 col-sm-3 col-xs-12">Konfirmasi Password</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label for="avatar" class="control-label col-md-3 col-sm-3 col-xs-12">Avatar</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="file" id="avatar" name="avatar" class="form-control {{$errors->first('name') != '' ? 'parsley-error' : ''}}">
					@if($index->avatar != '')
						<img src="{{ asset($index->avatar) }}" style="height: 100px;"/>
						<input type="checkbox" name="remove_avatar"> Remove Avatar
					@endif
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('avatar') }}</li>
					</ul>
				</div>
			</div>
			
			<div class="form-group">
				<label for="active" class="control-label col-md-3 col-sm-3 col-xs-12">Status</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="active" name="active" class="form-control {{$errors->first('active') != '' ? 'parsley-error' : ''}}">
						<option value="1" @if($index->active == 1) selected @endif>Active</option>
						<option value="0" @if($index->active == 0) selected @endif>Incative</option>
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('active') }}</li>
					</ul>
				</div>
			</div>

			@foreach($key as $list)
			<div class="form-group">
				<label class="control-label checkbox-inline col-md-3 col-sm-3 col-xs-12">
					<input type="checkbox" data-target="group-{{ $list['id'] }}" class="check-all"> Access {{ $list['name'] }}
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					@foreach($list['data'] as $list2)
					<label class="checkbox-inline"><input type="checkbox" name="permission[]" class="group-{{ $list['id'] }}" value="{{ $list2['value'] }}" @if(in_array($list2['value'], old('permission', explode(', ', $index->permission)))) checked @endif>{{ $list2['name'] }}</label>
					@endforeach
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('permission') }}</li>
					</ul>
				</div>
			</div>
			@endforeach
			

			<div class="form-group">
				<label for="password_user" class="control-label col-md-3 col-sm-3 col-xs-12">Password User <span class="required">*</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="password" id="password_user" name="password_user" class="form-control {{$errors->first('password_user') != '' ? 'parsley-error' : ''}}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('password_user') }}</li>
					</ul>
				</div>
			</div>
			

			<div class="ln_solid"></div>
			<div class="form-group">
				<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
					{{ csrf_field() }}
					<a class="btn btn-primary" href="{{ route('admin.user') }}">Batal</a>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>

		</form>
	</div>
	

@endsection