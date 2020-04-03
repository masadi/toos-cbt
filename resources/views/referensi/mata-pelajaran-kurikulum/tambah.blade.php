@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Tambah Data Mata Pelajaran Kurikulum
                    </div>
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
                    <form id="form" class="form-horizontal"
                        action="{{ route('referensi.simpan', ['query' => 'mata-pelajaran-kurikulum']) }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Jurusan</label>
                                <div class="col-md-9">
                                    <select name="jurusan_id" id="jurusan_id" class="select2 form-control">
                                        <option value="">== Pilih ==</option>
                                        @foreach ($data_jurusan as $jurusan)
                                        <option value="{{$jurusan->jurusan_id}}">{{$jurusan->nama_jurusan}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Kurikulum</label>
                                <div class="col-md-9">
                                    <select name="kurikulum_id" id="kurikulum_id" class="select2 form-control">
                                        <option value="">== Pilih ==</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Tingkat Pendidikan</label>
                                <div class="col-md-9">
                                    <select name="tingkat_pendidikan_id" id="tingkat_pendidikan_id"
                                        class="select2 form-control">
                                        <option value="">== Pilih ==</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Mata Pelajaran</label>
                                <div class="col-md-9">
                                    <select name="mata_pelajaran_id" id="mata_pelajaran_id"
                                        class="select2 form-control">
                                        <option value="">== Pilih ==</option>
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
    $('#jurusan_id').change(function(){
        var ini = $(this).val();
        $('#kurikulum_id').prop("selectedIndex", 0);
		$("#kurikulum_id").trigger('change.select2');
        $('#mata_pelajaran_id').prop("selectedIndex", 0);
		$("#mata_pelajaran_id").trigger('change.select2');
        $('#tingkat_pendidikan_id').prop("selectedIndex", 0);
		$("#tingkat_pendidikan_id").trigger('change.select2');
        if(ini == ''){
            return false;
        } else {
            $.ajax({
                url: '{{route('ajax.get_data', ['query' => 'mata-pelajaran-kurikulum'])}}',
                type: 'post',
                data: $("#form").serialize(),
                success: function(response){
                    $("#kurikulum_id").html('<option value="">== Pilih ==</option>');
                    if(!$.isEmptyObject(response.results)){
						$.each(response.results, function (i, item) {
							$('#kurikulum_id').append($('<option>', { 
								value: item.id,
								text : item.text
							}));
						});
					}
                }
            });
		}
    });
    $('#kurikulum_id').change(function(){
        var ini = $(this).val();
        $('#mata_pelajaran_id').prop("selectedIndex", 0);
		$("#mata_pelajaran_id").trigger('change.select2');
        $('#tingkat_pendidikan_id').prop("selectedIndex", 0);
		$("#tingkat_pendidikan_id").trigger('change.select2');
        if(ini == ''){
            return false;
        } else {
            $.ajax({
                url: '{{route('ajax.get_data', ['query' => 'tingkat-pendidikan'])}}',
                type: 'post',
                data: $("#form").serialize(),
                success: function(response){
                    $("#tingkat_pendidikan_id").html('<option value="">== Pilih ==</option>');
                    if(!$.isEmptyObject(response.results)){
						$.each(response.results, function (i, item) {
							$('#tingkat_pendidikan_id').append($('<option>', { 
								value: item.id,
								text : item.text
							}));
						});
					}
                }
            });
		}
    });
    $('#mata_pelajaran_id').select2({
        ajax: {
            url: '{{route('ajax.get_all_data', ['query' => 'mata-pelajaran'])}}',
            minimumInputLength: 5,
            dataType: 'json',
            placeholder: 'Cari Mata Pelajaran',
        }
    });
</script>
@endsection