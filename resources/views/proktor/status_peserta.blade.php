@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Status Peserta
                        <div class="card-header-actions">
                            <button class="btn btn-primary start_upload has-spinner" data-text="UPLOADING....">UPLOAD HASIL</button>
                            <button class="btn btn-danger reset_hasil has-spinner" data-text="RESETTING....">RESET UJIAN</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-responsive-sm table-outline mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" width="10px"><input type="checkbox" name="select_all" value="1" id="datatable-select-all"></th>
                                    <th>Nama</th>
                                    <th>Mata Ujian</th>
                                    <th>Status Ujian</th>
                                    <th>Status Upload</th>
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
<link href="{{ asset('assets/btn/buttonLoader.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('javascript')
<script src="{{ asset('assets/dataTables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/btn/jquery.buttonLoader.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
    $(function() {
    var table = null;
    function init() {
        table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{route('ajax.get_all_data', ['query' => 'status-peserta'])}}',
                data:function(data) {
                    data.exam_id = '{{config('global.exam_id')}}';
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', className: 'dt-body-center', orderable: false, searchable: false },
                { data: 'nama', name: 'user_exam_id' },
                { data: 'mata_ujian', name: 'exam.nama' },
                { data: 'status_ujian', name: 'status_ujian', orderable: false, searchable: false },
                { data: 'status_upload', name: 'status_upload', orderable: false, searchable: false },
            ]
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
    init();
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
    function terpilih(){
        var selected = 0;
        var user_exam_id = [];
        var anggota_rombel_id = [];
        var ptk_id = [];
        // Iterate over all checkboxes in the table
        var all_user_exam_id = $('input.user_exam_id');
        var exam_id = $('input.exam_id').val();
        table.$('input.anggota_rombel_id').each(function(k,v){
            if(this.checked){
                anggota_rombel_id.push(this.value);
                selected++;
            }
        });
        table.$('input.ptk_id').each(function(k,v){
            if(this.checked){
                ptk_id.push(this.value);
                selected++;
            }
        });
        table.$('input[type="checkbox"]').each(function(k,v){
            if(this.checked){
                user_exam_id.push($(all_user_exam_id[k]).val());
                selected++;
            }
        });
        return selected;
    }
    $('.reset_hasil').click(function(e){
        e.preventDefault();
        var selected = 0;
        var btn = $(this);
        $(btn).buttonLoader('start', $(this).data('text'));
        var user_exam_id = [];
        var anggota_rombel_id = [];
        var ptk_id = [];
        // Iterate over all checkboxes in the table
        var all_user_exam_id = $('input.user_exam_id');
        var exam_id = $('input.exam_id').val();
        table.$('input.anggota_rombel_id').each(function(k,v){
            if(this.checked){
                anggota_rombel_id.push(this.value);
                selected++;
            }
        });
        table.$('input.ptk_id').each(function(k,v){
            if(this.checked){
                ptk_id.push(this.value);
                selected++;
            }
        });
        table.$('input[type="checkbox"]').each(function(k,v){
            if(this.checked){
                user_exam_id.push($(all_user_exam_id[k]).val());
                selected++;
            }
        });
        if(selected){
            $.ajax({
                url: '{{route('proktor.simpan', ['query' => 'reset-hasil'])}}',
                type: 'post',
                data: {exam_id: exam_id, anggota_rombel_id: anggota_rombel_id, ptk_id:ptk_id, user_exam_id:user_exam_id},
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
        Swal.fire({
            icon: 'error',
            text: 'Pengiriman hasil ujian belum diaktifkan',
        });
        return false;
        var selected = 0;
        var btn = $(this);
        $(btn).buttonLoader('start', $(this).data('text'));
        var user_exam_id = [];
        var anggota_rombel_id = [];
        var ptk_id = [];
        // Iterate over all checkboxes in the table
        var all_user_exam_id = $('input.user_exam_id');
        var exam_id = $('input.exam_id').val();
        /*table.$('input[type="checkbox"]').each(function(k,v){
            if(this.checked){
                user_exam_id.push($(all_user_exam_id[k]).val());
                anggota_rombel_id.push(this.value);
                selected++;
            }
        });*/
        table.$('input.anggota_rombel_id').each(function(k,v){
            if(this.checked){
                anggota_rombel_id.push(this.value);
                selected++;
            }
        });
        table.$('input.ptk_id').each(function(k,v){
            if(this.checked){
                ptk_id.push(this.value);
                selected++;
            }
        });
        table.$('input[type="checkbox"]').each(function(k,v){
            if(this.checked){
                user_exam_id.push($(all_user_exam_id[k]).val());
                selected++;
            }
        });
        if(selected){
            $.ajax({
                url: '{{route('proktor.simpan', ['query' => 'upload-hasil'])}}',
                type: 'post',
                data: {exam_id: exam_id, anggota_rombel_id: anggota_rombel_id, ptk_id:ptk_id, user_exam_id:user_exam_id},
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