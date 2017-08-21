@extends('backend.layout.master')

@section('title')
	User Management - Edit
@endsection

@section('script')

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
				<label for="password_confirmation" class="control-label col-md-3 col-sm-3 col-xs-12">Confirmation Password</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label for="user_password" class="control-label col-md-3 col-sm-3 col-xs-12">Current User Password <span class="required">*if update password</span>
				</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<input type="password" id="user_password" name="user_password" class="form-control {{$errors->first('user_password') != '' ? 'parsley-error' : ''}}">
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('user_password') }}</li>
					</ul>
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
			
			@if(Auth::user()->access == 0)
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
			<div class="form-group">
				<label for="access" class="control-label col-md-3 col-sm-3 col-xs-12">Access</label>
				<div class="col-md-9 col-sm-9 col-xs-12">
					<select id="access" name="access" class="form-control {{$errors->first('access') != '' ? 'parsley-error' : ''}}">
						<option value="0" @if($index->access == 0) selected @endif>Semua</option>
						<option value="1" @if($index->access == 1) selected @endif>Semua, Kecuali Buat User Dan Ubah User Lain</option>
					</select>
					<ul class="parsley-errors-list filled">
						<li class="parsley-required">{{ $errors->first('access') }}</li>
					</ul>
				</div>
			</div>
			@endif
			
			

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