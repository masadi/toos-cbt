@extends('dashboard.base')

@section('content')

<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>Dashboard</h2>
                    </div>
                    <div class="card-body">
                        <h2 class="text-success"><strong>AKTIF</strong></h2>
                        <div class="alert alert-success" role="alert">Aplikasi siap digunakan</div>
                        <!-- /.row--><br>
                    </div>
                </div>
            </div>
            <!-- /.col-->
        </div>
        <!-- /.row-->
    </div>
</div>
@endsection
@section('javascript')
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
$.get("{{route('proktor.index', ['query' => 'check-update'])}}", function( data ) {
    if(data.update){
        Swal.fire({
            icon: 'info',
            text: 'Update Tersedia',
            confirmButtonText: 'Download',
            allowOutsideClick: false,
        }).then(function(e) {
            Swal.fire({
                title: "Downloading...",
                text: "Please wait",
                imageUrl: "assets/img/ajax-loader.gif",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            $.get("{{route('proktor.index', ['query' => 'download-update'])}}", function( data ) {
                Swal.fire({
                    icon: data.icon,
                    text: data.message,
                    confirmButtonText: 'Refresh',
                    allowOutsideClick: false,
                }).then(function(e) {
                    window.location.replace(window.location.href);
                });
            });
        });
    }
});
</script>
@endsection