@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">Data Sekolah</div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-hover">
                            <tr>
                                <td>Nama</td>
                                <td>{{$data->nama}}</td>
                            </tr>
                            <tr>
                                <td>NPSN</td>
                                <td>{{$data->npsn}}</td>
                            </tr>
                            <tr>
                                <td>Bentuk Pendidikan</td>
                                <td>{{$data->bentuk_pendidikan->nama}}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>{{$data->alamat}}</td>
                            </tr>
                            <tr>
                                <td>Desa/Kelurahan</td>
                                <td>{{$data->desa_kelurahan}}</td>
                            </tr>
                            <tr>
                                <td>Kecamatan</td>
                                <td>{{$data->kecamatan}}</td>
                            </tr>
                            <tr>
                                <td>Kabupaten</td>
                                <td>{{$data->kabupaten}}</td>
                            </tr>
                            <tr>
                                <td>Provinsi</td>
                                <td>{{$data->provinsi}}</td>
                            </tr>
                            <tr>
                                <td>Kodepos</td>
                                <td>{{$data->kode_pos}}</td>
                            </tr>
                            <tr>
                                <td>Nomor Telepon</td>
                                <td>{{$data->no_telp}}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{$data->email}}</td>
                            </tr>
                            <tr>
                                <td>Website</td>
                                <td>{{$data->website}}</td>
                            </tr>
                            <tr>
                                <td>Status Sekolah</td>
                                <td>{{($data->status_sekolah == 1) ? 'Negeri' : 'Swasta'}}</td>
                            </tr>
                            <tr>
                                <td>Lisensi</td>
                                <td>{{$data->lisensi}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection