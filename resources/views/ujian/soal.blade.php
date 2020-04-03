@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Data Soal Mata Ujian {{$ujian->nama}}
                        <div class="card-header-actions">
                            <a class="btn btn-sm btn-success" href="{{route('ujian.tambah_data', ['query' => 'soal', 'ujian_id' => $ujian->exam_id])}}">Tambah Data</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Isi Soal</th>
                                    <th>Aksi</th>
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

@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('assets/dataTables/jquery.dataTables.min.css') }}">
@endsection
@section('javascript')
<script src="{{ asset('assets/dataTables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
    $(function() {
    var table = null;
    function init() {
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{route('ajax.get_all_soal', ['ujian_id' => $ujian->exam_id])}}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'question', name: 'question' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            fnDrawCallback: function(oSettings) {
                turn_on_icheck();
            }
        });
    }
    init();
    // Called if protocol handler is not installed
    function protocolInstallCallback(message) {
        var installerFilePath = "/Plugins/" + ITHit.WebDAV.Client.DocManager.GetInstallFileName();
        if (confirm("This action requires a protocol installation. Select OK to download the protocol installer.")){
            window.open(installerFilePath);
        }
    }
    function turn_on_icheck() {
        $('a.toggle-edit').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            //"https://server/folder/file.docx"
            ITHit.WebDAV.Client.DocManager.EditDocument(url, "/", protocolInstallCallback);
        });
        $('a.toggle-modal').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            if (url.indexOf('#') == 0) {
                $('#modal_content').modal('open');
            } else {
                $.get(url, function(data) {
                    $('#modal_content').modal();
                    $('#modal_content').html(data);
                });
            }
        });
        $('a.toggle-delete').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: "Anda Yakin?",
                text: "Tindakan ini tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
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
});
</script>
@endsection