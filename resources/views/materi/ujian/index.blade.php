@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Data Ujian
                        @hasanyrole('sekolah|ptk')
                        <div class="card-header-actions">
                            <a class="btn btn-sm btn-success"
                                href="{{route('materi.tambah_data', ['query' => 'ujian'])}}">Tambah Data</a>
                        </div>
                        @endrole
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Ujian</th>
                                    <th>Mata Pelajaran</th>
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
<script type="text/javascript" src="{{ asset('assets/ITHitWebDAVClient.js') }}" ></script>
@endsection
@section('javascript')
<script src="{{ asset('assets/dataTables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
    $(function() {
    var table = null;
    var rombongan_belajar_id = '{{($user->peserta_didik_id) ? $user->peserta_didik->anggota_rombel->rombongan_belajar_id: NULL}}';
    function init() {
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route('ajax.get_all_materi', ['query' => 'ujian'])}}',
                data:function(data) {
					data.sekolah_id = '{{$user->sekolah_id}}';
                    data.rombongan_belajar_id = rombongan_belajar_id;
				}
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'pembelajaran.nama_mata_pelajaran', name: 'pembelajaran.nama_mata_pelajaran' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            fnDrawCallback: function(oSettings) {
                turn_on_icheck();
            }
        });
    }
    init();
    function turn_on_icheck() {
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