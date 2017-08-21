@extends('backend.layout.master')

@section('title')
  <title>Kingdom Finance | Not Found</title>
@endsection

@section('content')

<div class="col-md-12">
  <div class="col-middle">
    <div class="text-center text-center">
      <h1 class="error-number">404</h1>
      <h2>Halaman yang anda cari tidak ada</h2>
      {{-- <p>This page you are looking for does not exist <a href="#">Report this?</a> --}}
      </p>
      <div class="mid_center">
        <h3>Kembali</h3>
        <form>
          <div class="col-xs-12 form-group pull-right top_search">
            {{-- <div class="input-group">
              <input type="text" class="form-control" placeholder="Search for...">
              <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                  </span>
            </div> --}}
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
