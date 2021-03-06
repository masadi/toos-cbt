@extends('adminlte::page')

@section('title', 'STATUS PESERTA | TOOS CBT V.3.x')

@section('content_header')
<h1 class="m-0 text-dark">STATUS PESERTA</h1>
@stop
@section('content')
<div class="card">
    <div class="card-header">
        Status Peserta
        <div class="card-tools">
            <button class="btn btn-sm btn-primary start_upload has-spinner" data-text="UPLOADING....">UPLOAD
                HASIL</button>
            <button class="btn btn-sm btn-danger reset_hasil has-spinner" data-text="RESETTING....">RESET
                UJIAN</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row" style="margin-bottom:10px;">
            <div class="col-sm-6">
                <select id="sekolah_id" class="form-control select2">
                    <option value="">== Semua Sekolah ==</option>
                    @foreach($all_sekolah as $sekolah)
                    <option value="{{$sekolah->sekolah_id}}">{{$sekolah->nama}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <select id="rombongan_belajar_id" class="form-control select2">
                    <option value="">== Semua Rombongan Belajar ==</option>
                </select>
            </div>
        </div>
        <div class="table-no-responsive">
            <table id="datatable" class="table table-outline mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" width="10px"><input type="checkbox" name="select_all" value="1"
                                id="datatable-select-all"></th>
                        <th>Nama</th>
                        <th>Rombel</th>
                        <th>Mata Ujian</th>
                        <th>Status Ujian</th>
                        <th>Sisa Waktu</th>
                        <th>Status Upload</th>
                        <th>Force Selesai</th>
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
    var table = null;
    function init(sekolah_id, rombongan_belajar_id) {
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route('ajax.get_all_data', ['query' => 'status-peserta'])}}',
                data:function(data) {
                    data.sekolah_id = sekolah_id;
                    data.rombongan_belajar_id = rombongan_belajar_id;
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', className: 'dt-body-center', orderable: false, searchable: false },
                { data: 'user', name: 'user.name' },
                { data: 'nama_rombel', name: 'nama_rombel' },
                { data: 'mata_ujian', name: 'exam.nama' },
                { data: 'status_ujian', name: 'status_ujian', orderable: false, searchable: false },
                { data: 'sisa_waktu', name: 'sisa_waktu', className: 'dt-body-center' },
                { data: 'status_upload', name: 'status_upload', orderable: false, searchable: false },
                { data: 'force_selesai', name: 'status_ujian', className: 'dt-body-center', searchable: false },
            ],
            fnDrawCallback: function(oSettings) {
                turn_on_icheck();
            },
            fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if(iDisplayIndex == 0){
                    if(aData.rombongan_belajar.query){
                        $("#rombongan_belajar_id").html('<option value="">== Semua Rombongan Belajar ==</option>');
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
            /*columnDefs: [
                {
                    targets: 0,
                    searchable: false,
                    orderable: false,
                    className: 'dt-body-center',
                    render: function (data, type, full, meta){
                        if(full.status_ujian == 'Selesai'){
                            if(full.status_upload == 'Belum Terupload'){
                                if(full.anggota_rombel_id){
                                    return '<input type="checkbox" class="anggota_rombel_id" name="anggota_rombel_id[' + full.user_exam_id + '][]" value="' + full.anggota_rombel_id + '">';
                                } else {
                                    return '<input type="checkbox" class="ptk_id" name="ptk_id[' + full.user_exam_id + '][]" value="' + full.ptk_id + '">';
                                }
                            } else {
                                if(full.anggota_rombel_id){
                                    return '<input type="checkbox" name="anggota_rombel_id[]" value="' + full.anggota_rombel_id + '" disabled>';
                                } else {
                                    return '<input type="checkbox" name="ptk_id[]" value="' + full.ptk_id + '" disabled>';
                                }
                            }
                        } else {
                            if(full.anggota_rombel_id){
                                return '<input type="checkbox" name="anggota_rombel_id[]" value="' + full.anggota_rombel_id + '" disabled>';
                            } else {
                                return '<input type="checkbox" name="ptk_id[]" value="' + full.ptk_id + '" disabled>';
                            }
                        }
                    }
                },
                {
                    targets: 1,
                    render: function (data, type, full, meta){
                        if(full.anggota_rombel){
                            return '<input class="user_exam_id" type="hidden" name="user_exam_id[]" value="' + full.user_exam_id + '">'+full.anggota_rombel.peserta_didik.nama;
                        } else {
                            return '<input class="user_exam_id" type="hidden" name="user_exam_id[]" value="' + full.user_exam_id + '">'+full.ptk.nama;
                        }
                    }
                },
                {
                    targets: 2,
                    render: function (data, type, full, meta){
                        return '<input class="exam_id" type="hidden" name="exam_id" value="' + full.exam_id + '">'+full.exam.nama;
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, full, meta){
                        return full.status_ujian;
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, full, meta){
                        return full.status_upload;
                    }
                }
            ]*/
        });
    }
    //init();
    init(null, null);
    $('#sekolah_id').change(function(){
        $("#rombongan_belajar_id").html('<option value="">== Semua Rombongan Belajar ==</option>');
        var ini = $(this).val();
        table.destroy();
        init(ini, null);
    });
    $('#rombongan_belajar_id').change(function(){
        var ini = $(this).val();
        var sekolah_id = $('#sekolah_id').val();
        table.destroy();
        init(sekolah_id, ini);
    });
    function turn_on_icheck() {
        $('a.force_selesai').bind('click', function(e) {
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
    }
    $('#datatable-select-all').on('click', function(){
        // Get all rows with search applied
        var rows = table.rows({ 'search': 'applied' }).nodes();
        // Check/uncheck checkboxes for all rows in the table
        $('input[type="checkbox"]:enabled', rows).prop('checked', this.checked);
    });
    // Handle click on checkbox to set state of "Select all" control
    $('#datatable tbody').on('change', 'input[type="checkbox"]', function(){
        // If checkbox is not checked
        if(!this.checked){
            var el = $('#datatable-select-all').get(0);
            // If "Select all" control is checked and has 'indeterminate' property
            if(el && el.checked && ('indeterminate' in el)){
                // Set visual state of "Select all" control
                // as 'indeterminate'
                el.indeterminate = true;
            }
        }
    });
    $('.reset_hasil').click(function(e){
        e.preventDefault();
        var selected = 0;
        var btn = $(this);
        $(btn).buttonLoader('start', $(this).data('text'));
        var user_exam_id = [];
        table.$('input[type="checkbox"]').each(function(k,v){
            if(this.checked){
                user_exam_id.push(this.value);
                selected++;
            }
        });
        if(selected){
            $.ajax({
                url: '{{route('proktor.simpan', ['query' => 'reset-hasil'])}}',
                type: 'post',
                data: {user_exam_id:user_exam_id},
                success: function(data){
                    //var data = $.parseJSON(response);
                    Swal.fire({
                        icon: data.icon,
                        text: data.message,
                    }).then(function(e) {
                        $(btn).buttonLoader('stop');
                        table.ajax.reload( null, false );
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                text: 'Silahkan checklist peserta terlebih dahulu!',
                confirmButtonText: 'Refresh'
            }).then(function(e) {
                $(btn).buttonLoader('stop');
            });
        }
    });
    $('.start_upload').click(function(e){
        e.preventDefault();
        var selected = 0;
        var btn = $(this);
        $(btn).buttonLoader('start', $(this).data('text'));
        var user_exam_id = [];
        table.$('input[type="checkbox"]').each(function(k,v){
            if(this.checked){
                user_exam_id.push(this.value);
                selected++;
            }
        });
        if(selected){
            $.ajax({
                url: '{{route('proktor.simpan', ['query' => 'upload-hasil'])}}',
                type: 'post',
                data: {user_exam_id:user_exam_id},
                success: function(response){
                    var data = $.parseJSON(response);
                    Swal.fire({
                        icon: data.icon,
                        text: data.message,
                    }).then(function(e) {
                        $(btn).buttonLoader('stop');
                        table.ajax.reload( null, false );
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                text: 'Silahkan checklist peserta terlebih dahulu!',
                confirmButtonText: 'Refresh'
            }).then(function(e) {
                $(btn).buttonLoader('stop');
            });
        }
    });
});
</script>
@endsection