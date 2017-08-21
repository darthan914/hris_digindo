<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Tipe" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HRIS DIGINDO | Login</title>

    <link href="{{ asset('backend/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="https://colorlib.com/polygon/gentelella/css/animate.min.css" rel="stylesheet">

    <link href="{{ asset('backend/css/custom.min.css') }}" rel="stylesheet">
  </head>

  <body class="login">
    <div>
    @if(Session::has('failed'))
      <script>
      window.setTimeout(function() {
        $(".alert-danger").fadeTo(700, 0).slideUp(700, function(){
          $(this).remove();
        });
      }, 5000);
      </script>
      <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-12 col-md-offset-4">
          <div class="alert alert-danger alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            <strong>{{ Session::get('failed') }}</strong>
          </div>
        </div>
      </div>
    @endif

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form action="{{ route('admin.login') }}" method="POST">
              <h1>Login Form</h1>
              {{ csrf_field() }}
              <div class="form-group has-feedback {{ $errors->has('username') ? 'has-error' : '' }}">
                <input name="username" type="text" class="form-control" placeholder="Username" value="{{ old('username') }}">
                @if ($errors->has('username'))
                  <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @endif
              </div>
              <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                <input name="password" type="password" class="form-control" placeholder="Password" value="{{ old('password') }}">
                @if ($errors->has('password'))
                  <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @endif
              </div>
              <div class="row">
                <div class="col-xs-12">
                  <button type="submit" class="btn btn-primary btn-block btn-flat">Log In</button>
                </div>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <div>
                  <h1>HRIS EDigindo</h1>
                  <p>©2017 All Rights Reserved.</p>
                </div>
              </div>
            </form>
          </section>
        </div>

      </div>
    </div>
  </body>
</html>