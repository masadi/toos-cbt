@extends('dashboard.modal')
@section('title')
{{$title}}
@endsection
@section('content')
<table class="table table-responsive-sm table-hover">
    <tr>
        <td>Nama</td>
        <td>{{$data->nama}}</td>
    </tr>
    <tr>
        <td>Sekolah</td>
        <td>{{$data->sekolah->nama}}</td>
    </tr>
    <tr>
        <td>NISN</td>
        <td>{{$data->nisn}}</td>
    </tr>
    <tr>
        <td>Nomor Induk</td>
        <td>{{$data->no_induk}}</td>
    </tr>
    <tr>
        <td>NIK</td>
        <td>{{$data->nik}}</td>
    </tr>
    <tr>
        <td>Jenis Kelamin</td>
        <td>{{($data->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan'}}</td>
    </tr>
    <tr>
        <td>Tempat Lahir</td>
        <td>{{$data->tempat_lahir}}</td>
    </tr>
    <tr>
        <td>Tanggal Lahir</td>
        <td>{{Helper::TanggalIndo($data->tanggal_lahir)}}</td>
    </tr>
    <tr>
        <td>Agama</td>
        <td>{{$data->agama->nama}}</td>
    </tr>
    <tr>
        <td>Alamat</td>
        <td>{{$data->alamat}}</td>
    </tr>
    <tr>
        <td>Nomor Telepon</td>
        <td>{{$data->no_hp}}</td>
    </tr>
    <tr>
        <td>Email</td>
        <td>{{$data->email}}</td>
    </tr>
    <tr>
        <td>Foto</td>
        <td>{{$data->photo}}</td>
    </tr>
</table>
@endsection