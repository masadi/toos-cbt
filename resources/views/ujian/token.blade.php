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
                        <h4 class="float-left">Konfirmasi Data Peserta</h4>
                        <div class="card-header-actions">
                            <a class="btn btn-danger" href="{{url('/')}}">Kembali</a>
                        </div>
                    </div>
                    <div class="card-header">
                        <span class="font-lg"><strong>Nama Peserta</strong></span>
                        <br>
                        <span class="font-lg">{!!strtoupper($user->name)!!}</span>
                    </div>
                    @role('peserta_didik')
                    <div class="card-header">
                        <span class="font-lg"><strong>Rombongan Belajar</strong></span>
                        <br>
                        <span class="font-lg">{!!$user->peserta_didik->anggota_rombel->rombongan_belajar->nama!!}</span>
                    </div>
                    <div class="card-header">
                        <span class="font-lg"><strong>Jenis Kelamin</strong></span>
                        <br>
                        <span class="font-lg">{!!($user->peserta_didik->jenis_kelamin == 'L') ? 'Laki-laki' :
                            'Perempuan'!!}</span>
                    </div>
                    @else
                    <div class="card-header">
                        <span class="font-lg"><strong>NUPTK</strong></span>
                        <br>
                        <span class="font-lg">{!!$user->ptk->nuptk!!}</span>
                    </div>
                    <div class="card-header">
                        <span class="font-lg"><strong>NIP</strong></span>
                        <br>
                        <span class="font-lg">{!! $user->ptk->nip !!}</span>
                    </div>
                    @endif
                    <div class="card-header">
                        <span class="font-lg"><strong>Mata Ujian</strong></span>
                        <div class="list-group" id="list-tab" role="tablist">
                            @forelse ($all_ujian as $item)
                            <a href="#{{$item->exam_id}}" class="list-group-item list-group-item-action" data-toggle="list" data-exam_id="{{$item->exam_id}}" title="Klik untuk mengikuti Mata Ujian ini!"><span class="font-lg text-danger">{{$item->pembelajaran->nama_mata_pelajaran}} : {{$item->nama}}</span></a>
                            @empty
                            <a class="list-group-item"><span class="font-lg text-danger">Mata Ujian belum tersedia. Silahkan hubungi
                                Proktor</span></a>
                            @endforelse
                        </div>
                    </div>
                    <div class="card-header">
                        <span class="font-lg"><strong>Token</strong></span>
                        <br>
                        <input type="text" name="token" id="token" class="form-control form-control-lg col-sm-4">
                        <input type="hidden" name="exam_id" id="exam_id" value="">
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="submit btn btn-lg float-right btn-success px-5" disabled>SUBMIT</button>
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