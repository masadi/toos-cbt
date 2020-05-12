@extends('adminlte::page')

@section('title', 'DAFTAR PESERTA | TOOS CBT V.3.x')

@section('content_header')
<h1 class="m-0 text-dark">DAFTAR PESERTA</h1>
@stop
@section('content')
<div class="card">
    <div class="card-header">
        Daftar Peserta
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-sm-5">
                <select name="tingkat_pendidikan_id" id="tingkat_pendidikan_id" class="form-control select2">
                    <option value="">== Filter Tingkat Kelas ==</option>
                    @foreach($all_tingkat as $tingkat)
                    <option value="{{$tingkat->tingkat}}">Tingkat {{$tingkat->tingkat}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-5">
                <select name="rombongan_belajar_id" id="rombongan_belajar_id" class="form-control select2">
                    <option value="">== Filter Rombongan Belajar ==</option>
                </select>
            </div>
            <div class="col-sm-2">
                <a class="kirim-pengguna btn btn-success btn-block" href="{{route('proktor.index', ['query' => 'kirim-akun'])}}">Kirim Pengguna</a>
            </div>
        </div>
        <table id="datatable" class="table table-responsive-sm table-outline mb-0">
            <thead class="thead-light">
                <tr>
                    <th class="text-center" width="10px">No</th>
                    <th>Nama</th>
                    <th class="text-center">L/K</th>
                    <th class="text-center">Kelas</th>
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
    $('.select2').select2({theme:'bootstrap4'});
    $(function() {
        $('.kirim-pengguna').click(function(e){
            e.preventDefault();
            var rombongan_belajar_id = $('#rombongan_belajar_id').val();
            if(rombongan_belajar_id == ''){
                Swal.fire({
                    icon: 'error',
                    text: 'Rombongan Belajar tidak boleh kosong!',
                    title: 'Gagal',
                });
                return false;
            } else {
                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                Swal.fire({
                    title: 'Anda Yakin?',
                    text: "Proses ini akan mengirim akses pengguna ke email peserta didik!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Lanjutkan',
                    showLoaderOnConfirm: true,
                    preConfirm: (login) => {
                        return fetch($(this).attr('action'), {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json, text-plain, */*",
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": token
                            },
                            body:JSON.stringify({
                                rombongan_belajar_id: $('#rombongan_belajar_id').val(),
                            })
                        }).then(response => {
                            console.log(response)
                            if (!response.ok) {
                                throw new Error(response.statusText)
                            }
                            return response.json()
                        }).catch(error => {
                            Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    console.log(result.value);
                    if (result.value) {
                        Swal.fire({
                            title: result.value.title,
                            text: result.value.status,
                            icon: result.value.icon
                        })
                    }
                })
            }
        });
        var table = null;
    function init(tingkat_pendidikan_id, rombongan_belajar_id) {
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route('ajax.get_all_data', ['query' => 'peserta-didik'])}}',
                data:function(data) {
                    data.sekolah_id = '{{$user->sekolah_id}}';
                    data.tingkat_pendidikan_id = tingkat_pendidikan_id;
                    data.rombongan_belajar_id = rombongan_belajar_id;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'dt-body-center', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'j_k', name: 'j_k', className: 'dt-body-center' },
                { data: 'kelas', name: 'kelas', className: 'dt-body-center' },
                { data: 'user.username', name: 'user.username', className: 'dt-body-center' },
                { data: 'password', name: 'password', className: 'dt-body-center', orderable: false, searchable: false },
            ],
            fnDrawCallback: function(oSettings) {
                turn_on_icheck();
            },
            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if(iDisplayIndex == 0){
                    if(aData.rombongan_belajar.query){
                        $("#rombongan_belajar_id").html('<option value="">== Filter Rombongan Belajar ==</option>');
                        $.each(aData.rombongan_belajar.result, function (i, item) {
                            if(item.id){
                                $('#rombongan_belajar_id').append($('<option>', { 
                                    value: item.id,
                                    text : item.text
                                }));
                            }
                        });
                    }
                }
            }
        });
    }
    init(null, null);
    $('#tingkat_pendidikan_id').change(function(){
        $("#rombongan_belajar_id").html('<option value="">== Filter Rombongan Belajar ==</option>');
        var ini = $(this).val();
        if(ini != ''){
            table.destroy();
            init(ini, null);
        }
    });
    $('#rombongan_belajar_id').change(function(){
        var ini = $(this).val();
        var tingkat_pendidikan_id = $('#tingkat_pendidikan_id').val();
        if(ini != ''){
            table.destroy();
            init(tingkat_pendidikan_id, ini);
        }
    });
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