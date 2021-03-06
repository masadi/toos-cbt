@extends('adminlte::page')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Data Hasil Ujian
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-hover table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Soal</th>
                                    <th>Jawaban</th>
                                    <th>Kunci</th>
                                    <th>Status</th>
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
@section('plugins.dataTables', true)
@section('plugins.Sweetalert2', true)
@section('js')
<script>
    $(function() {
    var table = null;
    function init() {
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route('ajax.get_detil_hasil_ujian', ['id' => $ujian->user_exam->user_exam_id])}}',
                //sekolah_id: '{{$user->sekolah_id}}'
                data:function(data) {
                    data.anggota_rombel_id = '{{$user->peserta_didik->anggota_rombel->anggota_rombel_id}}';
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'soal', name: 'soal' },
                { data: 'jawaban', name: 'jawaban', className: 'dt-body-center' },
                { data: 'kunci', name: 'kunci', className: 'dt-body-center' },
                { data: 'status', name: 'status', orderable: false, searchable: false }
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