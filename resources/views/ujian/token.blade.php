@extends('adminlte::page')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form id="konfirmasi" action="{!!route('ujian.konfirmasi')!!}">
                <div class="card mata-ujian p-6">
                    <div class="card-header">
                        <h4 class="float-left">Konfirmasi Data Peserta</h4>
                        <div class="card-tools">
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
                        <div class="form-group" style="margin-top:5px;">
                            <select name="exam_id" id="exam_id" class="form-control form-control-lg select2">
                                <option value="">== Pilih Mata Ujian ==</option>
                                @forelse ($all_ujian as $item)
                                @if($item->pembelajaran)
                                <option value="{{$item->exam_id}}">{{$item->pembelajaran->nama_mata_pelajaran}} : {{$item->nama}} | {{$item->pembelajaran->rombongan_belajar->nama}}</option>
                                @else
                                <option value="{{$item->exam_id}}">{{$item->nama}}</option>
                                @endif
                                @empty
                                <option value="">Mata Ujian belum tersedia. Silahkan hubungi Proktor</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="card-header">
                        <span class="font-lg"><strong>Token</strong></span>
                        <br>
                        <input type="text" name="token" id="token" class="form-control form-control-lg col-sm-4">
                        <!--input type="hidden" name="exam_id" id="exam_id" value=""-->
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
@section('plugins.Select2', true)
@section('plugins.Sweetalert2', true)
@section('js')
<script>
$(function() {
    $('.select2').select2({theme:'bootstrap4'});
    $('#exam_id').change(function(){
        var ini = $(this).val();
        if(ini == ''){
            $('.submit').prop("disabled", true);
        } else {
            $('.submit').prop("disabled", false);
        }
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