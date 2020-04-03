@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Tambah Data Server
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
                        action="{{ route('referensi.simpan', ['query' => 'server']) }}" method="post">
                        @csrf
                        <input type="hidden" name="sekolah_id" value="{{$sekolah->sekolah_id}}">
                        <input type="hidden" name="npsn" value="{{$sekolah->npsn}}">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Rombongan Belajar</label>
                                <div class="col-md-9">
                                    <select name="rombongan_belajar_id" id="rombongan_belajar_id" class="select2 form-control">
                                        <option value="">== Pilih ==</option>
                                        <option value="1">Multi Rombongan Belajar</option>
                                        @foreach ($rombongan_belajar as $rombel)
                                        <option value="{{$rombel->rombongan_belajar_id}}">{{$rombel->nama}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Simpan</button>
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
</script>
@endsection