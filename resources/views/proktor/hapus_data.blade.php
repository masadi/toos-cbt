@extends('adminlte::page')

@section('title', 'HAPUS DATA | TOOS CBT V.3.x')

@section('content_header')
<h1 class="m-0 text-dark">HAPUS DATA</h1>
@stop
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="float-left">HAPUS DATA CBT</h3>
    </div>
    <div class="card-body">
        SEDANG DALAM PENGEMBANGAN
    </div>
</div>
@endsection
@section('plugins.Sweetalert2', true)
@section('js')
<script>
    $(function() {
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
                            window.location.replace('{{url('hapus-data')}}');
                        });
                    });
                }
            }
        });
    });
});
</script>
@endsection