@extends('dashboard.base')

@section('content')

<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">SELAMAT DATANG {{strtoupper($user->name)}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                @if($user->photo)
                                <img src="{{ env('APP_URL') }}/storage/uploads/profile/200/{{$user->photo}}"
                                    alt="{{$user->name}}">
                                @else
                                @role('peserta_didik')
                                <img src="{{ env('APP_URL') }}/assets/img/avatars/{{($user->peserta_didik->jenis_kelamin == 'L') ? 'male' : 'female' }}-md.png" alt="{{$user->name}}">
                                @else
                                <img src="{{ env('APP_URL') }}/assets/img/avatars/male-md.png" alt="{{$user->name}}">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
@endsection