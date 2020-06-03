@extends('adminlte::page')

@section('title', 'STATUS TEST | TOOS CBT V.3.x')

@section('content_header')
<h1 class="m-0 text-dark">STATUS TEST</h1>
@stop
@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-tools">
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
                @if($all_ujian)
                <div class="col-md-5">
                    <select name="ujian_id" id="ujian_id" class="form-control select2" style="width:100%;">
                        <option value="">== Pilih Mata Pelajaran ==</option>
                        @foreach ($all_ujian as $ujian)
                            <option value="{{$ujian->id}}">{{$ujian->event->nama}} - {{$ujian->mata_pelajaran->nama}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <select name="exam_id" id="exam_id" class="form-control select2" style="width:100%;">
                        <option value="">== Pilih Mata Ujian ==</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success btn-block" type="submit"> Tambah</button>
                </div>
                @else 
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
                @endif
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
        @if($all_ujian)
        <table id="datatable" class="table table-outline mb-0">
            <thead class="thead-light">
                <tr>
                    <th class="text-center" width="10px">No</th>
                    <th>Nama Mata Ujian</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        @else 
        <table id="datatable" class="table table-outline mb-0">
            <thead class="thead-light">
                <tr>
                    <th class="text-center" width="10px">No</th>
                    <th>Nama Mata Ujian</th>
                    <th class="text-center">Jumlah Soal</th>
                    <th>Rombel</th>
                    <th>Mata Pelajaran</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
@section('plugins.Sweetalert2', true)
@section('plugins.Select2', true)
@section('plugins.Datatables', true)
@section('js')
<script>
$(function() {
    $('.select2').select2({theme:'bootstrap4'});
    var oTable = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{route('ajax.get_all_data', ['query' => 'ujian-aktif'])}}',
        @if($all_ujian)
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'dt-body-center', orderable: false, searchable: false },
            { data: 'nama', name: 'nama' },
            { data: 'status', name: 'status', className: 'dt-body-center', orderable: false, searchable: false },
            { data: 'toggle', name: 'toggle', className: 'dt-body-center', orderable: false, searchable: false },
        ],
        @else
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'dt-body-center', orderable: false, searchable: false },
            { data: 'nama', name: 'nama' },
            { data: 'question_count', name: 'question_count' },
            { data: 'pembelajaran.rombongan_belajar.nama', name: 'pembelajaran.rombongan_belajar.nama' },
            { data: 'pembelajaran.nama_mata_pelajaran', name: 'pembelajaran.nama_mata_pelajaran' },
            { data: 'status', name: 'status', className: 'dt-body-center', orderable: false, searchable: false },
            { data: 'toggle', name: 'toggle', className: 'dt-body-center', orderable: false, searchable: false },
        ],
        @endif
        fnDrawCallback: function(oSettings) {
            turn_on_icheck();
        }
    });
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
                                oTable.ajax.reload( null, false );
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
                                oTable.ajax.reload( null, false );
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
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        Swal.fire({
            title: 'Aktifkan Ujian',
            text: "Proses ini akan menggenerate file json ujian peserta didik!",
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
                        pembelajaran_id: $('#pembelajaran_id').val(),
                        exam_id: $('#exam_id').val()
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
                }).then(function(e){
                    oTable.ajax.reload( null, false );
                })
            }
        })
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
    $('#ujian_id').change(function(){
        var ini = $(this).val();
        if(ini == ''){
            return false;
        } else {
            $.ajax({
                url: '{{route('ajax.get_data', ['query' => 'mata-ujian-event'])}}',
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