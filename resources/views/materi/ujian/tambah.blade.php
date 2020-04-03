@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Tambah Data Ujian
                    </div>
                    <form id="form" class="form-horizontal" action="{{ route('materi.simpan', ['query' => 'ujian']) }}"
                        method="post" enctype="multipart/form-data">
                        <div class="card-body">
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
                            @csrf
                            <input type="hidden" name="mata_pelajaran_id" id="mata_pelajaran_id">
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Mata Pelajaran</label>
                                    <div class="col-md-9">
                                        <select name="pembelajaran_id" id="pembelajaran_id"
                                            class="select2 form-control">
                                            <option value="">== Pilih ==</option>
                                            @foreach ($pembelajaran as $mapel)
                                            <option value="{{$mapel->pembelajaran_id}}"
                                                data-mata_pelajaran_id="{{$mapel->mata_pelajaran_id}}">
                                                {{$mapel->nama_mata_pelajaran}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Nama Mata Ujian</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id="nama" type="text" name="nama"
                                            placeholder="Nama Mata Ujian">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="start">Tanggal Mulai Ujian</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id="start" type="date" name="start"
                                            placeholder="date">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="end">Tanggal Berakhir Ujian</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id="end" type="date" name="end" placeholder="date">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Jumlah Soal</label>
                                    <div class="col-md-9">
                                        <input class="form-control" id="jumlah_soal" type="text" name="jumlah_soal"
                                            placeholder="Jumlah Soal">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Opsi Jawaban</label>
                                    <div class="col-md-9">
                                        <select name="jumlah_opsi" id="jumlah_opsi" class="select2 form-control">
                                            <option value="">== Pilih ==</option>
                                            @for($i=1;$i<=5;$i++) <option value="{{$i}}">
                                                {{Helper::generateAlphabet($i - 1)}}</option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Durasi Ujian</label>
                                    <div class="col-md-9">
                                        <div class="controls">
                                            <div class="input-group">
                                                <input class="form-control" id="durasi" size="16" type="text"
                                                    name="durasi">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Menit</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                                <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                            </div>
                        </div>
                    </form>
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
        $('#pembelajaran_id').change(function(){
        var mata_pelajaran_id = $('option:selected', this).data('mata_pelajaran_id');
            $('#mata_pelajaran_id').val(mata_pelajaran_id);
        });
    $('#asd').change(function(){
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