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
                @role('proktor')
                <h2 class="text-success"><strong>AKTIF</strong></h2>
                <div class="alert alert-success" role="alert">Aplikasi siap digunakan</div>
                @endrole
                @role('peserta_didik')
                <div class="row">
                    <div class="col-md-2">
                        @if($user->photo)
                        <img src="{{ env('APP_URL') }}/storage/uploads/profile/200/{{$user->photo}}"
                            alt="{{$user->name}}">
                        @else
                        @role('peserta_didik')
                        <img src="{{ env('APP_URL') }}/vendor/img/avatars/{{($user->peserta_didik->jenis_kelamin == 'L') ? 'male' : 'female' }}-md.png"
                            alt="{{$user->name}}">
                        @else
                        <img src="{{ env('APP_URL') }}/vendor/img/avatars/male-md.png" alt="{{$user->name}}">
                        @endif
                        @endif
                    </div>
                    <div class="col-md-5">
                        <table class="table table-responsive-sm table-sm">
                            <tr>
                                <td>Nama</td>
                                <td>: {{strtoupper($user->name)}}</td>
                            </tr>
                            @role('peserta_didik')
                            <tr>
                                <td>NISN</td>
                                <td>: {{strtoupper($user->peserta_didik->nisn)}}</td>
                            </tr>
                            <tr>
                                <td>Tempat, Tanggal Lahir</td>
                                <td>: {{strtoupper($user->peserta_didik->tempat_lahir)}},
                                    {{Helper::TanggalIndo($user->peserta_didik->tanggal_lahir)}}</td>
                            </tr>
                            <tr>
                                <td>Rombongan Belajar</td>
                                <td>:
                                    {{strtoupper($user->peserta_didik->anggota_rombel->rombongan_belajar->nama)}}
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td>NUTPK</td>
                                <td>: {{strtoupper($user->ptk->nuptk)}}</td>
                            </tr>
                            <tr>
                                <td>NIP</td>
                                <td>:
                                    {{strtoupper($user->ptk->nip)}}
                                </td>
                            </tr>
                            <tr>
                                <td>Tempat, Tanggal Lahir</td>
                                <td>: {{strtoupper($user->ptk->tempat_lahir)}},
                                    {{Helper::TanggalIndo($user->ptk->tanggal_lahir)}}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-5">
                        <div class="font-lg"><strong>Informasi Aplikasi</strong></div>
                        <div>Nama Aplikasi : {{config('global.app_name')}}</div>
                        <div>Versi Aplikasi : {{config('global.app_version')}}</div>
                        <div>Versi Database : {{config('global.db_version')}}</div>
                        <div>Periode Aktif : {{Helper::semester()}}</div>
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
</div>
@stop