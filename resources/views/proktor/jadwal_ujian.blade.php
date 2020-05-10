@extends('adminlte::page')

@section('title', 'JADWAL UJIAN | TOOS CBT V.3.x')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">JADWAL UJIAN</h1>
    </div><!-- /.col -->
    <div class="col-sm-6">
        <a class="tambah_jadwal float-right btn btn-warning" href="{{route('proktor.index', ['query' => 'tambah-jadwal'])}}">Tambah Data</a>
    </div><!-- /.col -->
</div>
@stop
@section('content')
<div class="card">
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
                <a class="cetak btn btn-success btn-block" href="javascript:void(0)">Cetak Kartu Peserta</a>
            </div>
        </div>
        <div class="table-no-responsive">
            <table id="datatable" class="table table-outline mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Rombel</th>
                        <th>Mapel</th>
                        <th>Tanggal</th>
                        <th>Jam Mulai</th>
                        <th>Jam Berakhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)
@section('plugins.ButtonLoader', true)
@section('plugins.Datatables', true)
@section('js')
<script>
    $(function() {
    $('.select2').select2({theme:'bootstrap4'});
    $('.tambah_jadwal').click(function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        $.get("{{route('ajax.get_all_data', ['query' => 'rombel'])}}", function(data, status){
            console.log(status);
            Swal.fire({
                title: "Pilih Rombel",
                input: 'select',
                inputOptions: data.rombongan_belajar,
                inputPlaceholder: 'Pilih Rombongan Belajar',
                confirmButtonText: 'Pilih',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    return new Promise((resolve) => {
                        if (value) {
                            $.get(url, { rombongan_belajar_id: value } ).done(function( data ) {
                                $('#modal_content').modal({backdrop: 'static', keyboard: false});
                                $('#modal_content').html(data);
                                resolve()
                            });
                        } else {
                            resolve('Rombongan Belajar tidak boleh kosong')
                        }
                    })
                }
            })
        });
    });
    $('.cetak').click(function(e){
        var rombongan_belajar_id = $('#rombongan_belajar_id').val();
        if(rombongan_belajar_id == ''){
            Swal.fire({
                icon: 'error',
                text: 'Rombongan Belajar tidak boleh kosong!',
                title: 'Gagal',
            });
        } else {
            window.open('{{url('cetak-kartu')}}/'+rombongan_belajar_id, '_blank');
        }
    });
    var table = null;
    function init(sekolah_id, tingkat, rombongan_belajar_id) {
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route('ajax.get_all_data', ['query' => 'jadwal-ujian'])}}',
                data:function(data) {
                    data.sekolah_id = sekolah_id;
                    data.tingkat = tingkat;
                    data.rombongan_belajar_id = rombongan_belajar_id;
                }
            },
            columns: [
                { data: 'rombongan_belajar.nama', name: 'rombongan_belajar.nama' },
                { data: 'pembelajaran.nama_mata_pelajaran', name: 'pembelajaran.nama_mata_pelajaran' },
                { data: 'tanggal', name: 'tanggal' },
                { data: 'from', name: 'from', className:'text-center' },
                { data: 'to', name: 'to', className:'text-center' },
                { data: 'action', name: 'action', className: 'dt-body-center', searchable: false },
            ],
            fnDrawCallback: function(oSettings) {
                turn_on_icheck();
            },
            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if(iDisplayIndex == 0){
                    if(aData.all_rombel.query){
                        $("#rombongan_belajar_id").html('<option value="">== Semua Rombongan Belajar ==</option>');
                        $.each(aData.all_rombel.result, function (i, item) {
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
    //init();
    init('{{$user->sekolah_id}}', null);
    $('#tingkat_pendidikan_id').change(function(){
        $("#rombongan_belajar_id").html('<option value="">== Filter Rombongan Belajar ==</option>');
        var ini = $(this).val();
        if(ini != ''){
            table.destroy();
            init('{{$user->sekolah_id}}', ini, null);
        }
    });
    $('#rombongan_belajar_id').change(function(){
        var ini = $(this).val();
        var tingkat_pendidikan_id = $('#tingkat_pendidikan_id').val();
        if(ini != ''){
            table.destroy();
            init('{{$user->sekolah_id}}', tingkat_pendidikan_id, ini);
        }
    });
    function turn_on_icheck() {
        $('a.confirm').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: "Anda Yakin?",
                text: "Tindakan ini tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya!'
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
        $('a.toggle-modal').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $.get(url, function(data) {
                $('#modal_content').modal({backdrop: 'static', keyboard: false});
                $('#modal_content').html(data);
            });
        });
    }
});
</script>
@endsection