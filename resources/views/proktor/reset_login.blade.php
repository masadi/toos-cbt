@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="float-left">Reset Login Peserta</h3>
                        <div class="card-header-actions">
                            <button class="btn btn-danger reset_all has-spinner" data-text="RESETING....">RESET ALL</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-responsive-sm table-outline mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" width="10px"><input type="checkbox" name="select_all" value="1" id="datatable-select-all"></th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Kelas</th>
                                    <th>Mata Ujian</th>
                                    <th>Reset</th>
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
                url: '{{route('ajax.get_all_data', ['query' => 'reset-login'])}}',
                data:function(data) {
                    data.exam_id = '{{config('global.exam_id')}}';
                }
            },
            /*columnDefs: [
                {
                    targets: 0,
                    searchable: false,
                    orderable: false,
                    className: 'dt-body-center',
                    render: function (data, type, full, meta){
                        return '<input type="checkbox" name="user_id[]" value="' + full.user_id + '">';
                    }
                },
                {
                    targets: 1,
                    render: function (data, type, full, meta){
                        if(full.peserta_didik){
                            return full.peserta_didik.nama;
                        } else {
                            return full.ptk.nama;
                        }
                    }
                },
                {
                    targets: 2,
                    render: function (data, type, full, meta){
                        return full.username;
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, full, meta){
                        if(full.peserta_didik){
                            return full.peserta_didik.anggota_rombel.rombongan_belajar.nama;
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    targets: 4,
                    render: function (data, type, full, meta){
                        return full.mata_ujian;
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, full, meta){
                        return full.reset_login;
                    }
                }
            ],*/
            columns: [
                { data: 'checkbox', name: 'checkbox', className: 'dt-body-center', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'username', name: 'username' },
                { data: 'nama_rombongan_belajar', name: 'nama_rombongan_belajar' },
                { data: 'mata_ujian', name: 'mata_ujian' },
                { data: 'reset_login', name: 'reset_login', className: 'dt-body-center', orderable: false, searchable: false },
            ],
            fnDrawCallback: function(oSettings) {
                turn_on_icheck();
            }
        });
    }
    init();
    function turn_on_icheck() {
        $('a.reset_login').bind('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: "Anda Yakin?",
                text: "Tindakan ini tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Reset!'
            }).then((result) => {
                if (result) {
                    console.log(result);
                    if (result.value) {
                        $.get(url, function(data) {
                            Swal.fire({
                                icon: data.icon,
                                text: data.message,
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
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
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
    $('.reset_all').click(function(e){
        e.preventDefault();
        var selected = 0;
        var btn = $(this);
        $(btn).buttonLoader('start', $(this).data('text'));
        var users_id = [];
        table.$('input[type="checkbox"]').each(function(k,v){
            if(this.checked){
                users_id.push(this.value);
                //$(form).append($('<input>').attr('type', 'text').attr('name', this.name).val(this.value));
                selected++;
            }
        });
        if(selected){
            console.log('proses');
            Swal.fire({
                title: "Anda Yakin?",
                text: "Tindakan ini tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Reset!'
            }).then((result) => {
                if (result) {
                    console.log(result);
                    if (result.value) {
                        $.ajax({
                            url: '{{route('proktor.simpan', ['query' => 'reset-login'])}}',
                            type: 'post',
                            data: {users_id: users_id},
                            success: function(response){
                                Swal.fire({
                                    icon: response.icon,
                                    text: response.message,
                                }).then(function(e) {
                                    $(btn).buttonLoader('stop');
                                    table.ajax.reload( null, false );
                                });
                            }
                        });
                    }
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