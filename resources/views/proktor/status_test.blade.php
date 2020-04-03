@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-actions">
                            <a class="rilis_token btn btn-sm btn-danger" href="{{route('proktor.index', ['query' => 'rilis-token'])}}">RILIS TOKEN</a>
                        </div>
                        Mata Ujian Aktif 
                        <br><div id="token_ujian">{!!($token) ? $token : ''!!} </div>
                    </div>
                    <form id="form" class="form-inline" action="{{route('proktor.simpan', ['query' => 'ujian'])}}"
                        method="post">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="rombongan_belajar_id" id="rombongan_belajar_id" class="form-control select2" style="width:100%;">
                                        <option value="">== Pilih Rombongan Belajar ==</option>
                                        @foreach ($rombongan_belajar as $rombel)
                                            <option value="{{$rombel->rombongan_belajar_id}}">{{$rombel->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="pembelajaran_id" id="pembelajaran_id" class="form-control select2" style="width:100%;">
                                        <option value="">== Pilih Mata Pelajaran ==</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="exam_id" id="exam_id" class="form-control select2" style="width:100%;">
                                        <option value="">== Pilih Mata Ujian ==</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success btn-block" type="submit"> Tambah</button>
                                </div>
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
                        </div>
                    </form>
                    <div class="card-footer">
                        <table id="datatable" class="table table-outline mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" width="10px">No</th>
                                    <th>Nama Mata Ujian</th>
                                    <th>Rombel</th>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
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
            ajax: '{{route('ajax.get_all_data', ['query' => 'ujian-aktif'])}}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'dt-body-center', orderable: false, searchable: false },
                { data: 'nama', name: 'nama' },
                { data: 'pembelajaran.rombongan_belajar.nama', name: 'pembelajaran.rombongan_belajar.nama' },
                { data: 'pembelajaran.nama_mata_pelajaran', name: 'pembelajaran.nama_mata_pelajaran' },
                { data: 'status', name: 'status', className: 'dt-body-center', orderable: false, searchable: false },
                { data: 'toggle', name: 'toggle', className: 'dt-body-center', orderable: false, searchable: false },
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
            var exam_id = $(this).data('exam_id');
            Swal.fire({
                title: "Anda Yakin?",
                text: "Tindakan ini tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Non Aktifkan!'
            }).then((result) => {
                if (result) {
                    if (result.value) {
                        $.get(url, {exam_id:exam_id}).done(function(data) {
                            Swal.fire({
                                icon: data.icon,
                                text: data.status,
                            }).then(function(e) {
                                if(!data.count){
                                    $('#token_ujian').html('');
                                }
                                $('#rombongan_belajar_id').val('');
                                $("#pembelajaran_id").html('<option value="">== Pilih Mata Pelajaran ==</option>');
                                $("#exam_id").html('<option value="">== Pilih Mata Ujian ==</option>');
                                table.ajax.reload( null, false );
                            });
                        });
                    }
                }
            });
        });
    }
    $('.rilis_token').click(function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Opsi Token',
            input: 'select',
            inputOptions: {
                dinamis: 'Dinamis (Update per 15 Menit)',
                statis: 'Statis',
            },
            inputPlaceholder: '== Pilih Opsi Token ==',
            showCancelButton: true,
            inputValidator: (value) => {
                return new Promise((resolve) => {
                    if (value) {
                        $.get($(this).attr('href'), { opsi: value} ).done(function( data ) {
                            Swal.fire({
                                icon: data.icon,
                                text: data.status,
                            }).then(function(e) {
                                console.log(data.token);
                                $('#token_ujian').html(data.token);
                                $('#rombongan_belajar_id').val('');
                                $("#pembelajaran_id").html('<option value="">== Pilih Mata Pelajaran ==</option>');
                                $("#exam_id").html('<option value="">== Pilih Mata Ujian ==</option>');
                                table.ajax.reload( null, false );
                            });
                        });
                    } else {
                        resolve('Opsi Token tidak boleh kosong');
                    }
                    
                })
            }
        });
    });
    $("#form").submit(function(e){
        e.preventDefault();
        console.log($(this));
        $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                data: $(this).serialize(),
                success: function(response){
                    Swal.fire({
                        icon: response.icon,
                        text: response.status,
                    }).then(function(e) {
                        $('#rombongan_belajar_id').val('');
                        $("#pembelajaran_id").html('<option value="">== Pilih Mata Pelajaran ==</option>');
                        $("#exam_id").html('<option value="">== Pilih Mata Ujian ==</option>');
                        table.ajax.reload( null, false );
                    });
                }
            });
    })
    $('#rombongan_belajar_id').change(function(){
        var ini = $(this).val();
        if(ini == ''){
            return false;
        } else {
            $.ajax({
                url: '{{route('ajax.get_data', ['query' => 'pembelajaran'])}}',
                type: 'post',
                data: $("#form").serialize(),
                success: function(response){
                    $("#pembelajaran_id").html('<option value="">== Pilih Mata Pelajaran ==</option>');
                    if(!$.isEmptyObject(response.results)){
						$.each(response.results, function (i, item) {
							$('#pembelajaran_id').append($('<option>', { 
								value: item.id,
								text : item.text
							}));
						});
					}
                }
            });
		}
    });
    $('#pembelajaran_id').change(function(){
        var ini = $(this).val();
        if(ini == ''){
            return false;
        } else {
            $.ajax({
                url: '{{route('ajax.get_data', ['query' => 'mata-ujian'])}}',
                type: 'post',
                data: $("#form").serialize(),
                success: function(response){
                    $("#exam_id").html('<option value="">== Pilih Mata Ujian ==</option>');
                    if(!$.isEmptyObject(response.results)){
						$.each(response.results, function (i, item) {
							$('#exam_id').append($('<option>', { 
								value: item.id,
								text : item.text
							}));
						});
					}
                }
            });
		}
    });
});
</script>
@endsection