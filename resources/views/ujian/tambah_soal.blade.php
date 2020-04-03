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
                                aria-controls="manual">Tambah Data Soal (Manual)</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#bank" role="tab"
                                aria-controls="bank">Export Data Soal (Bank Soal)</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="manual" role="tabpanel">
                            <form id="form" class="form-horizontal"
                                action="{{ route('ujian.simpan', ['query' => 'soal', 'ujian_id' => $ujian->exam_id]) }}"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Isi Soal</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" id="soal" type="text" name="soal"
                                                placeholder="Isi Soal"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label">Isi Jawaban</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" id="jawaban" type="text" name="jawaban"
                                                placeholder="Isi jawaban"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                                    <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="bank" role="tabpanel">
                            <table id="datatable" class="table table-striped table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th>Isi Soal</th>
                                        <th>Tambahkan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/dataTables/jquery.dataTables.min.css') }}">
@endsection
@section('javascript')
<script src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/dataTables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
    $('.select2').select2();
    $(function() {
        var table = null;
        function init() {
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{route('ajax.get_all_materi', ['query' => 'bank-soal'])}}',
                    data:function(data) {
                        data.sekolah_id = '{{$ujian->pembelajaran->sekolah_id}}';
                        data.mata_pelajaran_id = '{{$ujian->mata_pelajaran_id}}';
                        data.ujian_id = '{{$ujian->exam_id}}';
                    }
                },
                columns: [
                    { data: 'soal', name: 'soal' },
                    { data: 'insert', name: 'insert', orderable: false, searchable: false }
                ],
                fnDrawCallback: function(oSettings) {
                    turn_on_icheck();
                }
            });
            function turn_on_icheck() {
                $('a.toggle-insert').bind('click', function(e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    Swal.fire({
                        title: "Anda Yakin?",
                        text: "Jika terjadi kesalahan, Anda bisa menghapusnya!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Tambahkan!'
                    }).then((result) => {
                        if (result) {
                            console.log(result);
                            if (result.value) {
                                $.get(url, function(data) {
                                    Swal.fire({
                                        icon: data.icon,
                                        text: data.status,
                                    }).then(function(e) {
                                        table.ajax.reload( null, false );
                                    });
                                });
                            }
                        }
                    });
                });
            }
        }
        init();
    });
</script>
@endsection