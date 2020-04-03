@extends('dashboard.authBase')

@section('content')

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card mx-4">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('register') }}">
            @if ($errors->any())
            <div class="alert alert-danger" role="alert">
              @foreach ($errors->all() as $error)
              {{ $error }}<br />
              @endforeach
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger" role="alert">
              {{ session('error') }}
            </div>
            @endif
            @if(!config('internet'))
            <div class="alert alert-danger" role="alert">
              VM tidak terkoneksi internet. Silahkan refresh atau cek konfigurasi network adapter
            </div>
            @endif
            @csrf
            <input type="hidden" name="tz" id="tz">
            <h1>{{ __('Aktifasi Lisensi') }}</h1>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <svg class="c-icon">
                    <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-bank"></use>
                  </svg>
                </span>
              </div>
              <input class="form-control" type="text" placeholder="{{ __('NPSN') }}" name="npsn"
                value="{{ old('npsn') }}" required autofocus>
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <svg class="c-icon">
                    <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-lock-unlocked"></use>
                  </svg>
                </span>
              </div>
              <input class="form-control" type="text" placeholder="{{ __('Lisensi') }}" name="lisensi"
                value="{{ old('lisensi') }}" required autofocus>
            </div>
            <button class="btn btn-lg btn-block btn-success" type="submit" {{(!config('internet')) ? 'disabled' : ''}}>{{ __('Aktivasi') }}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('javascript')
<script>
  $(function () {
      // guess user timezone 
      $('#tz').val(moment.tz.guess())
  })
</script>
@endsection