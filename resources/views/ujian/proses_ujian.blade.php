@extends('dashboard.base_ujian')
@section('content')
<?php 
$waktu_ujian = date('Y-m-d H:i:s', time() + ($ujian->durasi * 60));
if($user_exam->sisa_waktu){
    $str_time = $user_exam->sisa_waktu;
    $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
    sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
    $sisa_waktu = $hours * 3600 + $minutes * 60 + $seconds;
    $waktu_ujian = date('Y-m-d H:i:s', time() + $sisa_waktu);
}
$sisa_waktu_ujian = date('Y/m/d H:i:s', strtotime($waktu_ujian));
?>
<input type="hidden" id="ujian_id" value="{{$ujian->exam_id}}">
<div class="alert alert-success d-sm-block d-md-block d-lg-none" role="alert">
    <strong>Nomor Soal : <span id="nomor_soal_mini">1</span></strong>
    <br>
    Sisa Waktu : <span id="clock_mini"></span>
</div>
<button class="btn btn-square btn-danger d-sm-block d-md-block d-lg-none" type="button" data-toggle="collapse"
    data-target="#collapseSoal" aria-expanded="false" aria-controls="collapseExample">
    <span class="cil-applications btn-icon mr-2"></span>
</button>
<div id="load">
@include('ujian.load_soal')
</div>
<div class="loader" style="display:none;"></div>
@endsection
@section('css')
<!--link href="{{ asset('assets/ujian/jquery.toggleinput.css') }}" rel="stylesheet"-->
<link href="{{ asset('assets/ujian/ujian.css') }}" rel="stylesheet">
<link href="{{ asset('assets/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.css') }}" rel="stylesheet">
@endsection
@section('javascript')
<script src="{{ asset('assets/bootstrap4-toggle@3.6.1/bootstrap4-toggle.min.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('js/jquery.countdown.min.js') }}"></script>
<script src="{{ asset('assets/ujian/ujian.js') }}"></script>
<script src="{{ asset('assets/ujian/player.js') }}"></script>
<script src="{{ asset('assets/ujian/jquery.idle.min.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(function() {
        var idleTime = 60000 * 10;
        $(document).idle({
            onIdle: function(){
                window.location.replace('/signout');
            },
            idle: idleTime
        });
        $('#navigasi').on('show.coreui.collapse', function () {
            console.log('show');
            $('.sidenav').css({
                'right':'23.8%',
                'left': 'auto'
            });
        });
        $('#navigasi').on('hidden.coreui.collapse', function () {
            console.log('hidden');
            $('.sidenav').css({
                'right':'0px',
                'left': 'auto'
            });
        });   
        initPlayers(jQuery('#player-container').length);  
        checkPilihan();
        var nomor_soal = getUrlParameter(window.location);
        //nomor_soal = parseInt(nomor_soal) + 1;
        $('#nomor_soal').html(nomor_soal+' dari {{$ujian->question_count}} soal');
        $('#nomor_soal_mini').html(nomor_soal+' dari {{$ujian->question_count}} soal');
        $('#clock_mini').countdown('{{$sisa_waktu_ujian}}').on('update.countdown', function(event) {
            var format = '%H:%M:%S';
            if(event.offset.totalDays > 0) {
                format = '%-d day%!d ' + format;
            }
            if(event.offset.weeks > 0) {
                format = '%-w week%!w ' + format;
            }
            $(this).html(event.strftime(format));
        });
        $('#clock').countdown('{{$sisa_waktu_ujian}}').on('update.countdown', function(event) {
            var format = '%H:%M:%S';
            if(event.offset.totalDays > 0) {
                format = '%-d day%!d ' + format;
            }
            if(event.offset.weeks > 0) {
                format = '%-w week%!w ' + format;
            }
            $(this).html(event.strftime(format));
            $('#sisa_waktu').val(event.strftime(format));
        }).on('finish.countdown', function(event) {
            var ujian_id = $('#ujian_id').val();
            var question_id = $('#question_id').val();
            var answer_id = $("input[name='answer_id']:checked").val();
            if(!answer_id){
                answer_id = 'kosong';
            }
            var sisa_waktu = $('#sisa_waktu').val();
            $.ajax({
                url : '{{route('ujian.selesai')}}',
                data: {ujian_id:ujian_id, question_id:question_id, answer_id:answer_id, sisa_waktu:sisa_waktu}
            }).done(function (data) {
                Swal.fire({
                    icon: 'error',
                    text: 'Waktu Habis!',
                    allowOutsideClick: false,
                }).then(function(e) {
                    window.location.replace("{{url('/')}}");
                });
            });
        });
        $('body').on('click', 'a.navigasi', function(e) {
            e.preventDefault();
            //$('#load a').css('color', '#dfecf6');
            //$('#load').append('<img style="position: fixed; left: 0; top: 0; z-index: 9999;" src="/assets/img/ajax-loader.gif" />');
            //$('.loader').show();
            var url = $(this).attr('href');  
            getExams(url);
            //window.history.pushState("", "", url);
        });
        $('body').on('click', 'button.btn-navigasi', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            //$('#load a').css('color', '#dfecf6');
            //$('#load').append('<img style="position: fixed; left: 0; top: 0; z-index: 9999;" src="/assets/img/ajax-loader.gif" />');
            //$('.loader').show();
            getExams(url);
            //window.history.pushState("", "", url);
        });    
        function getExams(url) {
            var nomor_soal = getUrlParameter(url);
            if(nomor_soal == 0){
                nomor_soal = parseInt(nomor_soal) + 1;
            }
            $('#nomor_soal').html(nomor_soal+' dari {{$ujian->question_count}} soal');
            $('#nomor_soal_mini').html(nomor_soal+' dari {{$ujian->question_count}} soal');
            var ujian_id = $('#ujian_id').val();
            var question_id = $('#question_id').val();
            var answer_id = $("input[name='answer_id']:checked").val();
            if(!answer_id){
                answer_id = 'kosong';
            }
            var sisa_waktu = $('#sisa_waktu').val();
            var ragu = $('#ragu').val();
            var kunci = [];
            $('input[name=kunci]').each(function(k,v){
                kunci.push(this.value);
            });
            $.ajax({
                url : url,
                data: {ujian_id:ujian_id, question_id:question_id, answer_id:answer_id, sisa_waktu:sisa_waktu,ragu:ragu, keys:kunci}
            }).done(function (data) {
                console.log(data);
                $('#load').html(data);
                $('.loader').hide();
                checkPilihan();
            }).fail(function () {
                //alert('Articles could not be loaded.');
                Swal.fire({
                    icon: 'error',
                    text: 'Server tidak merespon. Silahkan refresh halaman ini!',
                    confirmButtonText: 'Refresh'
                }).then(function(e) {
                    window.location.replace(window.location.href);
                });
            });
            return false;
        }
        $('body').on('click', 'a.selesai', function(e) {
            e.preventDefault();
            var ujian_id = $('#ujian_id').val();
            var question_id = $('#question_id').val();
            var answer_id = $("input[name='answer_id']:checked").val();
            if(!answer_id){
                answer_id = 'kosong';
            }
            var sisa_waktu = $('#sisa_waktu').val();
            Swal.fire({
                title: 'Selesai Ujian',
                input: 'checkbox',
                inputValue: 0,
                inputPlaceholder: 'Saya yakin akan menyelesaikan proses ujian',
                confirmButtonText: 'Selesai<i class="fa fa-arrow-right"></i>',
                allowOutsideClick: false,
                showCancelButton: true,
                inputValidator: (result) => {
                    return !result && 'Anda harus menceklist terlebih dahulu!'
                }
            }).then(function(result) {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    return false;
                }
                $.ajax({
                    url : '{{route('ujian.selesai')}}',
                    data: {ujian_id:ujian_id, question_id:question_id, answer_id:answer_id, sisa_waktu:sisa_waktu}
                }).done(function (data) {
                    Swal.fire({
                        icon: 'success',
                        text: 'Ujian Selesai',
                        allowOutsideClick: false,
                    }).then(function(e) {
                        window.location.replace("{{url('/')}}");
                    });
                });
            });
        });
    });    
</script>
@endsection