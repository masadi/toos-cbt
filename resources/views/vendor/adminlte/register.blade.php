@extends('adminlte::master')

@section('adminlte_css')
@stack('css')
@yield('css')
@stop

@section('classes_body', 'register-page')

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
@php( $login_url = $login_url ? route($login_url) : '' )
@php( $register_url = $register_url ? route($register_url) : '' )
@php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
@php( $login_url = $login_url ? url($login_url) : '' )
@php( $register_url = $register_url ? url($register_url) : '' )
@php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('body')
<div class="register-box">
    <div class="register-logo">
        <a href="{{ $dashboard_url }}">{!! config('adminlte.logo', '<b>Admin</b>LTE') !!}</a>
    </div>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">AKTIFASI LISENSI</p>
            @if (Session::get('error'))
            <div class="alert alert-danger alert-block alert-dismissable"><i class="fa fa-ban"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Error</strong><br />
                {!! Session::get('error') !!}
            </div>
            @endif
            <form action="{{ $register_url }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="tz" id="tz">
                <div class="input-group mb-3">
                    <input type="text" name="npsn" class="form-control {{ $errors->has('npsn') ? 'is-invalid' : '' }}"
                        value="{{ old('npsn') }}" placeholder="NPSN" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-desktop"></span>
                        </div>
                    </div>

                    @if ($errors->has('npsn'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('npsn') }}</strong>
                    </div>
                    @endif
                </div>
                <div class="input-group mb-3">
                    <input type="text" name="lisensi"
                        class="form-control {{ $errors->has('lisensi') ? 'is-invalid' : '' }}"
                        value="{{ old('lisensi') }}" placeholder="LISENSI" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-university"></span>
                        </div>
                    </div>

                    @if ($errors->has('lisensi'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('lisensi') }}</strong>
                    </div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-flat">
                    {{ __('adminlte::adminlte.register') }}
                </button>
            </form>
            <p class="mt-2 mb-1">
                <a href="{{ $login_url }}">
                    {{ __('adminlte::adminlte.i_already_have_a_membership') }}
                </a>
            </p>
        </div><!-- /.card-body -->
    </div><!-- /.card -->
</div><!-- /.register-box -->
@stop

@section('adminlte_js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@stack('js')
@yield('js')
@stop