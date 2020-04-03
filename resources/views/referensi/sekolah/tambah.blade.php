@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
                @endif
                @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
                @endif
                @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                    {{ $error }}<br />
                    @endforeach
                </div>
                @endif
                <div class="nav-tabs-boxed">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#manual" role="tab"
                                aria-controls="manual">Tambah Data Sekolah</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#export" role="tab"
                                aria-controls="export">Export Data Sekolah</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="manual" role="tabpanel">
                            <form id="form" class="form-horizontal"
                                action="{{ route('referensi.simpan', ['query' => 'sekolah']) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" id="nama">Nama Sekolah</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id="nama" type="text" name="nama"
                                                placeholder="Nama Sekolah">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" id="npsn">NPSN</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id="npsn" type="text" name="npsn"
                                                placeholder="NPSN">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" id="bentuk_pendidikan_id">Bentuk
                                            Pendidikan</label>
                                        <div class="col-md-9">
                                            <select name="bentuk_pendidikan_id" id="bentuk_pendidikan_id"
                                                class="select2 form-control">
                                                <option value="">== Pilih ==</option>
                                                @foreach ($bentuk_pendidikan as $bentuk)
                                                <option value="{{$bentuk->bentuk_pendidikan_id}}">{{$bentuk->nama}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Provinsi</label>
                                        <div class="col-md-9">
                                            <select name="provinsi_id" id="provinsi_id" class="select2 form-control">
                                                <option value="">== Pilih ==</option>
                                                @foreach ($all_provinsi as $provinsi)
                                                <option value="{{$provinsi->kode_wilayah}}">{{$provinsi->nama}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Kabupaten</label>
                                        <div class="col-md-9">
                                            <select name="kabupaten_id" id="kabupaten_id" class="select2 form-control">
                                                <option value="">== Pilih ==</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Kecamatan</label>
                                        <div class="col-md-9">
                                            <select name="kecamatan_id" id="kecamatan_id" class="select2 form-control">
                                                <option value="">== Pilih ==</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Desa/Kelurahan</label>
                                        <div class="col-md-9">
                                            <select name="desa_kelurahan_id" id="desa_kelurahan_id" class="select2 form-control">
                                                <option value="">== Pilih ==</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="wilayah"></div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" id="kode_pos">Kodepos</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id="kode_pos" type="text" name="kode_pos"
                                                placeholder="Kodepos">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" id="no_telp">Nomor Telp/HP</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id="no_telp" type="text" name="no_telp"
                                                placeholder="Nomor Telp/HP">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" id="email">Email Sekolah</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id="email" type="text" name="email"
                                                placeholder="Email Sekolah">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" id="website">Website Sekolah</label>
                                        <div class="col-md-9">
                                            <input class="form-control" id="website" type="text" name="website"
                                                placeholder="Website Sekolah">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label" id="status_sekolah">Status
                                            Sekolah</label>
                                        <div class="col-md-9">
                                            <select name="status_sekolah" id="status_sekolah"
                                                class="select2 form-control">
                                                <option value="">== Pilih ==</option>
                                                <option value="1">Negeri</option>
                                                <option value="2">Swasta</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                                    <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="export" role="tabpanel">
                            <form action="{{ route('referensi.saveBulk', ['query' => 'sekolah']) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="file">File Excel</label>
                                    <input type="file" name="file" class="form-control" value="{{ old('file') }}"
                                        required>
                                    <p class="text-danger">{{ $errors->first('file') }}</p>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-sm">Upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection
    @section('css')
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
    @endsection
    @section('javascript')
    <script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
    <script>
        $('.select2').select2();
    $('#provinsi_id').change(function(){
        var ini = $(this).val();
        $('#kabupaten_id').prop("selectedIndex", 0);
		$("#kabupaten_id").trigger('change.select2');
        $('#kecamatan_id').prop("selectedIndex", 0);
		$("#kecamatan_id").trigger('change.select2');
        $('#desa_kelurahan_id').prop("selectedIndex", 0);
		$("#desa_kelurahan_id").trigger('change.select2');
        if(ini == ''){
            return false;
        } else {
            $.ajax({
                url: '{{route('ajax.get_wilayah')}}',
                type: 'post',
                data: $("#form").serialize(),
                success: function(response){
                    $("#kabupaten_id").html('<option value="">== Pilih ==</option>');
                    if(!$.isEmptyObject(response.results)){
						$.each(response.results, function (i, item) {
							$('#kabupaten_id').append($('<option>', { 
								value: item.id,
								text : item.text
							}));
						});
					}
                }
            });
		}
    });
    $('#kabupaten_id').change(function(){
        var ini = $(this).val();
        $('#kecamatan_id').prop("selectedIndex", 0);
		$("#kecamatan_id").trigger('change.select2');
        $('#desa_kelurahan_id').prop("selectedIndex", 0);
		$("#desa_kelurahan_id").trigger('change.select2');
        if(ini == ''){
            return false;
        } else {
            $.ajax({
                url: '{{route('ajax.get_wilayah')}}',
                type: 'post',
                data: $("#form").serialize(),
                success: function(response){
                    $("#kecamatan_id").html('<option value="">== Pilih ==</option>');
                    if(!$.isEmptyObject(response.results)){
						$.each(response.results, function (i, item) {
							$('#kecamatan_id').append($('<option>', { 
								value: item.id,
								text : item.text
							}));
						});
					}
                }
            });
		}
    });
    $('#kecamatan_id').change(function(){
        var ini = $(this).val();
        $('#desa_kelurahan_id').prop("selectedIndex", 0);
		$("#desa_kelurahan_id").trigger('change.select2');
        if(ini == ''){
            return false;
        } else {
            $.ajax({
                url: '{{route('ajax.get_wilayah')}}',
                type: 'post',
                data: $("#form").serialize(),
                success: function(response){
                    $("#desa_kelurahan_id").html('<option value="">== Pilih ==</option>');
                    if(!$.isEmptyObject(response.results)){
						$.each(response.results, function (i, item) {
							$('#desa_kelurahan_id').append($('<option>', { 
								value: item.id,
								text : item.text
							}));
						});
					}
                }
            });
		}
    });
    $('#desa_kelurahan_id').change(function(){
        var ini = $(this).val();
        $('#wilayah').html(ini);
    });
    </script>
    @endsection