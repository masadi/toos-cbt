@extends('adminlte::master')
@section('adminlte_css_pre')
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('adminlte_css')
@stack('css')
@yield('css')
@stop

@section('classes_body', 'layout-top-nav')
@section('body')
<div class="wrapper">
    @include('adminlte::partials.navbar.navbar-ujian')
    <div class="content-wrapper">
        <div class="content-header">
        </div>
        <div class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
    <footer class="main-footer">
        @yield('footer')
        <strong>Copyright &copy; {{date('Y')}} <a href="http://cyberelectra.co.id/" target="_blank">Cyber Electra
                &trade;</a>.</strong> All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Versi</b> {{config('global.app_version')}}
        </div>
    </footer>
</div>
@stop
@section('adminlte_js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@stack('js')
@yield('js')
@stop