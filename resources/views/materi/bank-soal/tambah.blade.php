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
                                aria-controls="manual">Tambah Data Bank Soal</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#export" role="tab"
                                aria-controls="export">Ambil Data Bank Soal</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="manual" role="tabpanel">
                            <form id="form" class="form-horizontal"
                                action="{{ route('materi.simpan', ['query' => 'bank-soal']) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="ptk_id_manual" id="ptk_id_manual">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Mata Pelajaran</label>
                                        <div class="col-md-9">
                                            <select name="mata_pelajaran_id" id="mata_pelajaran_id_manual"
                                                class="select2 form-control">
                                                <option value="">== Pilih ==</option>
                                                @foreach ($pembelajaran as $mapel)
                                                <option value="{{$mapel->mata_pelajaran_id}}" data-ptk_id="{{$mapel->ptk_id}}">
                                                    {{$mapel->nama_mata_pelajaran}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Isi Soal</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" id="soal" type="text" name="soal"
                                                placeholder="Isi Soal"></textarea>
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
                            <form class="form-horizontal" action="{{ route('materi.saveBulk', ['query' => 'bank-soal']) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="ptk_id_import" id="ptk_id_import">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Mata Pelajaran</label>
                                    <div class="col-md-9">
                                        <select name="mata_pelajaran_id" id="mata_pelajaran_id_import" class="select2 form-control" style="width:100%;">
                                            <option value="">== Pilih ==</option>
                                            @foreach ($pembelajaran as $mapel)
                                            <option value="{{$mapel->mata_pelajaran_id}}" data-ptk_id="{{$mapel->ptk_id}}">
                                                {{$mapel->nama_mata_pelajaran}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">File ZIP</label>
                                    <div class="col-md-9">
                                        <input type="file" name="file" class="form-control" value="{{ old('file') }}"
                                            required>
                                    </div>
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
    $('#mata_pelajaran_id_manual').change(function(){
        var ptk_id = $('option:selected', this).data('ptk_id');
        $('#ptk_id_manual').val(ptk_id);
    });
    $('#mata_pelajaran_id_import').change(function(){
        var ptk_id = $('option:selected', this).data('ptk_id');
        $('#ptk_id_import').val(ptk_id);
    });
    </script>
    @endsection