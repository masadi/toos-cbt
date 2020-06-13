@extends('adminlte::page')
@section('content')
<style>
    @media only screen and (max-width: 600px) {
        .control-sidebar{width: 100px;top: calc(6.5rem + 1px);}
        .control-sidebar-slide-open.control-sidebar-push .content-wrapper{margin-right: 100px;}
    }
</style>
<?php 
$waktu_ujian = date('Y-m-d H:i:s', strtotime($now) + ($ujian->durasi * 60));
if($user_exam->sisa_waktu){
    $str_time = $user_exam->sisa_waktu;
    $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
    sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
    $sisa_waktu = $hours * 3600 + $minutes * 60 + $seconds;
    $waktu_ujian = date('Y-m-d H:i:s', strtotime($now) + $sisa_waktu);
}
$sisa_waktu_ujian = date('Y/m/d H:i:s', strtotime($waktu_ujian));
?>
<input type="hidden" id="ujian_id" value="{{$ujian->exam_id}}">
<input type="hidden" id="user_id" value="{{$user->user_id}}">
@section('content_top_nav_center')
<div class="timer text-white text-center" style="display: none; margin-left:calc(25%);"><b>Sisa Waktu : <span id="clock"></span></b></div>
<input type="hidden" id="sisa_waktu">    
@endsection
@section('right-sidebar')
<div class="col-md-12 row col-12" style="margin-top: 40px;">
    
        @if($keys)
        @foreach($keys as $question_id)
        <input type="hidden" name="kunci" value="{{$question_id}}">
        <?php
        $a = $collect_jawaban->where('question_id', $question_id)->first();
        $jawaban = ($a) ? (object) $a : NULL;
        $soal = ($questions) ? $questions[0] : NULL;
        $soal_id = ($soal) ? $soal->question_id : NULL;
        $btn = 'btn-default';
        if($jawaban){
            if($jawaban->ragu){
                $btn = 'btn-warning';
            } else {
                if($jawaban->answer_id){
                    $btn = 'btn-success';
                }
            }
        }
        if($soal_id == $question_id){
            $btn = 'btn-secondary';
        }
        ?>
        <div class="col-sm-3 mb-1">
            <button data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                class="{{$question_id}} btn btn-block btn-navigasi {{$btn}}"
                type="button">{{$loop->iteration}}</button>
            <?php
            /*
            @if($jawaban)
                @if($jawaban->ragu)
                    <button data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                        class="{{$question_id}} btn btn-block btn-navigasi "
                        type="button">{{$loop->iteration}}</button>
                @else
                    @if($jawaban->answer_id)
                        <button data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                            class="{{$question_id}} btn btn-block btn-navigasi btn-success"
                            type="button">{{$loop->iteration}}</button>
                    @else
                        <button data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                            class="{{$question_id}} btn btn-block btn-navigasi btn-default"
                            type="button">{{$loop->iteration}}</button>
                    @endif
                @endif
            @else
            <button data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                class="{{$question_id}} btn btn-block btn-navigasi btn-default"
                type="button">{{$loop->iteration}}</button>
            @endif
            */
            ?>
        </div>
        <!--/loop-->
        @endforeach
        @else
        -
        @endif
    
</div>
@endsection
<div id="load">
    @include('ujian.load_soal')
</div>
<div class="loader" style="display:none;"></div>
@endsection
@section('plugins.Sweetalert2', true)
@section('plugins.Ujian', true)
@section('js')
<script>
    $(function() {
        var user_id = $('#user_id').val();
        $(document).bind("contextmenu",function(e){
            //return false;
        });
        $('.navbar-brand').attr('href', 'javascript:void');
        var idleTime = 60000 * 10;
        $(document).idle({
            onIdle: function(){
                //window.location.replace('/logout');
            },
            idle: idleTime
        });
        initPlayers(jQuery('#player-container').length);  
        checkPilihan();
        var nomor_soal = getUrlParameter(window.location);
        $('.name_text').html('SOAL NOMOR '+nomor_soal+' dari {{$ujian->question_count}} soal');
        $('#nomor_soal_mini').html(nomor_soal+' dari {{$ujian->question_count}} soal');
        $('.timer').show();
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
            selesaiUjian(1, user_id, ujian_id, question_id, answer_id, sisa_waktu);
        });
        $('body').on('click', 'a.navigasi', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');  
            getExams(url);
        });
        $('body').on('click', 'button.btn-navigasi', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            getExams(url);
        });
        function selesaiUjian(type, user_id, ujian_id, question_id, answer_id, sisa_waktu){
            if(type == 1){
                Swal.fire({
                    text: "Waktu Habis!",
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Simpan Ujian',
                    showLoaderOnConfirm: true,
                    preConfirm: (login) => {
                        return fetch('{{route('ujian.selesai')}}?user_id='+user_id+'&ujian_id='+ujian_id+'&question_id='+question_id+'&answer_id='+answer_id+'&sisa_waktu='+sisa_waktu)
                        .then(response => {
                            if (!response.ok) {
                            throw new Error(response.statusText)
                            }
                            return response.json()
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        Swal.fire({
                            icon: result.value.icon,
                            title: result.value.title,
                            text: result.value.text,
                            allowOutsideClick: false,
                        }).then(function(e) {
                            window.location.replace("{{url('/')}}");
                        });
                    }
                })
            } else {
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
                    },
                    preConfirm: (login) => {
                        return fetch('{{route('ujian.selesai')}}?user_id='+user_id+'&ujian_id='+ujian_id+'&question_id='+question_id+'&answer_id='+answer_id+'&sisa_waktu='+sisa_waktu)
                        .then(response => {
                            if (!response.ok) {
                            throw new Error(response.statusText)
                            }
                            return response.json()
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(function(result) {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        return false;
                    }
                    Swal.fire({
                        icon: result.value.icon,
                        title: result.value.title,
                        text: result.value.text,
                        allowOutsideClick: false,
                    }).then(function(e) {
                        if(result.value.icon == 'success'){
                            window.location.replace("{{url('/')}}");
                        }
                    });
                });
            }
        }
        function getExams(url) {
            $('body').css('opacity', '0.3');
            var nomor_soal = getUrlParameter(url);
            if(nomor_soal == 0){
                nomor_soal = parseInt(nomor_soal) + 1;
            }
            var ujian_id = $('#ujian_id').val();
            var question_id = $('#question_id').val();
            var answer_id = $("input[name='answer_id']:checked").val();
            if(!answer_id){
                answer_id = 'kosong';
                if($('.'+question_id).hasClass("btn-success")){
                    $('.'+question_id).removeClass('btn-secondary').addClass('btn-success');
                } else if($('.'+question_id).hasClass("btn-warning")){
                    $('.'+question_id).removeClass('btn-secondary').addClass('btn-warning');
                } else {
                    $('.'+question_id).removeClass('btn-warning').removeClass('btn-success').removeClass('btn-secondary').addClass("btn-default");
                }
            } else {
                $('.'+question_id).removeClass('btn-warning').removeClass('btn-default').removeClass('btn-secondary').addClass("btn-success");
            }
            var sisa_waktu = $('#sisa_waktu').val();
            var ragu = $('#ragu').val();
            if(ragu){
                $('.'+question_id).removeClass('btn-success').removeClass('btn-default').removeClass('btn-secondary').addClass("btn-warning");
            }
            var kunci = [];
            $('input[name=kunci]').each(function(k,v){
                kunci.push(this.value);
            });
            $.ajax({
                url : url,
                data: {ujian_id:ujian_id, user_id:user_id, question_id:question_id, answer_id:answer_id, sisa_waktu:sisa_waktu,ragu:ragu, keys:kunci}
            }).done(function (response) {
                $('body').css('opacity', '1');
                $('.name_text').html('SOAL NOMOR '+nomor_soal+' dari {{$ujian->question_count}} soal');
                $('#nomor_soal_mini').html(nomor_soal+' dari {{$ujian->question_count}} soal');
                $('.'+response.current_id).removeClass('btn-success').removeClass('btn-default').removeClass('btn-warning').addClass("btn-secondary");
                if(response.icon =='error'){
                    if(response.ujian){
                        Swal.fire({
                            icon: response.icon,
                            title: response.title,
                            text: response.text,
                            confirmButtonText: 'Refresh',
                            allowOutsideClick: false,
                        }).then(function(e) {
                            window.location.replace(window.location.href);
                        });
                    } else {
                        Swal.fire({
                            icon: response.icon,
                            title: response.title,
                            text: response.text,
                            confirmButtonText: 'Kembali ke Beranda',
                            allowOutsideClick: false,
                        }).then(function(e) {
                            window.location.replace('{{url('/')}}');
                        });
                    }
                } else {
                    if(response ===false){
                        Swal.fire({
                            icon: 'error',
                            text: 'Server tidak merespon. Silahkan refresh halaman ini!',
                            confirmButtonText: 'Refresh',
                            allowOutsideClick: false,
                        }).then(function(e) {
                            getExams(url);
                        });
                    } else {
                        $('#load').html(response.html);
                        checkPilihan();
                    }
                }
            }).fail(function () {
                $('body').css('opacity', '1');
                Swal.fire({
                    icon: 'error',
                    text: 'Server tidak merespon. Silahkan refresh halaman ini!',
                    confirmButtonText: 'Refresh',
                    allowOutsideClick: false,
                }).then(function(e) {
                    getExams(url);
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
            selesaiUjian(2, user_id, ujian_id, question_id, answer_id, sisa_waktu);
        });
    });    
</script>
@endsection