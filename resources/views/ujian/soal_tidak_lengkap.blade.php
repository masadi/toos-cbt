@extends('dashboard.base_ujian')
@section('css')
<link href="{!! asset('assets/ujian/jquery.toggleinput.css') !!}" rel="stylesheet">
<style>
    .sub_header {
        padding: 10px 0 0 10px;
        font-weight: bold;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form id="konfirmasi" action="{!!route('ujian.konfirmasi')!!}">
                <div class="card mata-ujian p-6">
                    <div class="card-header">
                        <h4 class="float-left">Mata Ujian Tidak Lengkap. Silahkan hubungi Proktor</h4>
                        <div class="card-header-actions">
                            <a class="btn btn-danger" href="{{url('/')}}">Kembali</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(function() {
    $('a[data-toggle="list"]').on('show.coreui.tab', function (e) {
        var target = e.target;
        var exam_id = $(target).data('exam_id');
        $('#exam_id').val(exam_id);
        $('.submit').prop("disabled", false);
    })
    $("#konfirmasi").submit(function(e) {
        e.preventDefault();
        console.log('submit');
        console.log($(this));
        $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize(),
            success: function(response){
                console.log(response);
                if(response.success){
                    window.location.replace('{{url('/ujian/proses')}}/'+response.exam_id);
                } else {
                    Swal.fire({
                        icon: response.icon,
                        text: response.status,
                    });
                }
            }
        });
      // validate and process form here
    });
});        
</script>
@endsection