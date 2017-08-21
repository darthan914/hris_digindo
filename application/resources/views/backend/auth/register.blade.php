<form method="post" action="{!! route('admin.register.save') !!}">
	<input type="text" name="username" value="{{ old('username') }}">
	<input type="text" name="email" value="{{ old('email') }}">
	<input type="password" name="password">
	<input type="password" name="password_confirmation">
	<input type="text" name="name" value="{{ old('name') }}">
	<button type="submit">Submit</button>
	{!! csrf_field() !!}
</form>

<ul>
	@foreach ($errors->all() as $message)
	<li>{!! $message !!}</li>
	@endforeach
</ul>