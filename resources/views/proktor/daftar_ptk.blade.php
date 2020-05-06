@extends('adminlte::page')

@section('title', 'DAFTAR PTK | TOOS CBT V.3.x')

@section('content_header')
<h1 class="m-0 text-dark">DAFTAR PTK</h1>
@stop
@section('content')
<div class="card">
    <div class="card-header">
        Daftar Peserta
    </div>
    <div class="card-body">
        <table id="datatable" class="table table-responsive-sm table-outline mb-0">
            <thead class="thead-light">
                <tr>
                    <th class="text-center" width="10px">No</th>
                    <th>Nama</th>
                    <th class="text-center">Username</th>
                    <th class="text-center">Password</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('js')
<script>
    $(function() {
        var table = null;
    function init() {
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route('ajax.get_all_data', ['query' => 'ptk'])}}',
                data:function(data) {
                    data.sekolah_id = '{{$user->sekolah_id}}';
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'dt-body-center', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'user.username', name: 'user.username', className: 'dt-body-center' },
                { data: 'password', name: 'password', className: 'dt-body-center', orderable: false, searchable: false },
            ],
            fnDrawCallback: function(oSettings) {
                turn_on_icheck();
            }
        });
    }
    init();
    function turn_on_icheck() {
        $('a.toggle-reset').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: "Anda Yakin?",
                text: "Tindakan ini tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Atur Ulang!'
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