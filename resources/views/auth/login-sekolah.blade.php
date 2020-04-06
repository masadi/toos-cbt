@extends('dashboard.authBase')

@section('content')

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card-group">
        <div class="card p-4">
          <div class="card-body">
            <h1>Login</h1>
            <p class="text-muted">Sign In to your account</p>
            @if (session('success'))
            <div class="alert alert-success" role="alert">
              {!! session('success') !!}
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger" role="alert">
              {!! session('error') !!}
            </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger" role="alert">
              @foreach ($errors->all() as $error)
              {!! $error !!}<br />
              @endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('sekolah_login') }}">
              @csrf
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <svg class="c-icon">
                      <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-user"></use>
                    </svg>
                  </span>
                </div>
                <input class="form-control" type="text" placeholder="{{ __('E-Mail Address') }}" name="email"
                  value="{{ old('email') }}" required autofocus>
              </div>
              <div class="input-group mb-4">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <svg class="c-icon">
                      <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-lock-unlocked"></use>
                    </svg>
                  </span>
                </div>
                <input class="form-control" type="password" placeholder="{{ __('Password') }}" name="password" required>
                <input type="hidden" name="remember_me" value="1">
              </div>
              <button class="btn btn-primary btn-block" type="submit">{{ __('Login') }}</button>
            </form>
          </div>
        </div>
        <div class="card text-white d-md-down-none" style="width:44%; background:#292929">
          <div class="card-body text-center">
            <div>
              <h2>Login Sekolah</h2>
              <br>
              <p><img src="{{asset('assets/img/logo.png')}}" alt="" title="" style="width:300px;"></p>
              <?php /* {!!($server) ? '<h4>'.$server->sekolah->nama.'</h4>' : ''!!} */ ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('javascript')

@endsection