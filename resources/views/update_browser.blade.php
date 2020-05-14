@extends('adminlte::page')

@section('title', 'TOOS CBT V.3.x')

@section('content_header')
<h1 class="m-0 text-dark">Dashboard</h1>
@stop
@section('content')
<?php
$user = Auth::user();
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @desktop
                <div class="alert alert-danger" role="alert">
                    Browser anda tidak support aplikasi ini, silahkan download google chrome di link berikut : <a href="https://www.google.com/intl/id_id/chrome/">https://www.google.com/intl/id_id/chrome/</a>
                </div>
                @elsedesktop
                    @ios
                    <div class="alert alert-danger" role="alert">
                        Browser anda tidak support aplikasi ini, silahkan download google chrome di link berikut : <a href="https://apps.apple.com/id/app/google-chrome/id535886823">https://apps.apple.com/id/app/google-chrome/id535886823</a>
                    </div>
                    @elseios
                    <div class="alert alert-danger" role="alert">
                        Browser anda tidak support aplikasi ini, silahkan download google chrome di link berikut : <a href="https://play.google.com/store/apps/details?id=com.android.chrome&hl=in">https://play.google.com/store/apps/details?id=com.android.chrome&hl=in</a>
                    </div>
                    @endios
                @enddesktop
            </div>
        </div>
    </div>
</div>
@stop