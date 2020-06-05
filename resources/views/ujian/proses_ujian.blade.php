@extends('adminlte::page')
@section('content')
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
@section('right-sidebar')
<div class="col-md-12 row" style="margin-top: 40px;">
    
        @if($keys)
        @foreach($keys as $question_id)
        <input type="hidden" name="kunci" value="{{$question_id}}">
        <?php
        /*$jawaban = \App\User_question::where(function($query) use ($question_id, $user){
            $query->where('question_id', $question_id);
            if($user->peserta_didik_id){
                $query->where('anggota_rombel_id', $user->peserta_didik->anggota_rombel->anggota_rombel_id);
            } else {
                $query->where('ptk_id', $user->ptk_id);
            }
        })->first();*/
        //$a = asset('public/user_question-'.$user->user_id.'-'.$question_id.'.json');
        $b = '';//@file_get_contents($a);
        //if(Storage::disk('public')->exists('user_question-'.$user->user_id.'-'.$question_id.'.json')){
        //$path = storage_path('app/public/user_question-'.$user->user_id.'-'.$question_id.'.json');
        //$path = storage_path('app/public/user_question-'.$user->user_id.'-'.$question_id.'.json');
        $path = Helper::exam_folder($user->user_id, $ujian->exam_id);
        dd($path);
        if(File::exists($path.'/'.$question_id.'.json')){
            $reader->open($path.'/'.$question_id.'.json');
            if ($reader->read()) {
                $b = collect($reader->value());
                $b = $b->toJson();
            }
        }
        $jawaban = NULL;
        if($b){
            $jawaban = json_decode($b);
        }
        echo $path;
        ?>
        <div class="col-sm-3 mb-1">
            @if($jawaban)
                @if($jawaban->ragu)
                    <button data-url="{{route('ujian.get_soal', ['page' => $loop->iteration,'soal_id' => $question_id])}}"
                        class="{{$question_id}} btn btn-block btn-navigasi btn-warning"
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
        $(document).bind("contextmenu",function(e){
            return false;
        });
        $('.navbar-brand').attr('href', 'javascript:void');
        var idleTime = 60000 * 10;
        $(document).idle({
            onIdle: function(){
                window.location.replace('/logout');
            },
            idle: idleTime
        });
        initPlayers(jQuery('#player-container').length);  
        checkPilihan();
        var nomor_soal = getUrlParameter(window.location);
        //nomor_soal = parseInt(nomor_soal) + 1;
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
            /*Swal.fire({
                icon: 'error',
                text: 'Waktu Habis!',
                allowOutsideClick: false,
            }).then(function(e) {
                //window.location.replace("{{url('/')}}");
                $.ajax({
                    url : '{{route('ujian.selesai')}}',
                    data: {ujian_id:ujian_id, question_id:question_id, answer_id:answer_id, sisa_waktu:sisa_waktu}
                }).done(function (data) {
                    window.location.replace("{{url('/')}}");
                });
            });*/
            selesaiUjian(1, ujian_id, question_id, answer_id, sisa_waktu);
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
        function selesaiUjian(type, ujian_id, question_id, answer_id, sisa_waktu){
            if(type == 1){
                Swal.fire({
                    text: "Waktu Habis!",
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Simpan Ujian',
                    showLoaderOnConfirm: true,
                    preConfirm: (login) => {
                        return fetch('{{route('ujian.selesai')}}?ujian_id='+ujian_id+'&question_id='+question_id+'&answer_id='+answer_id+'&sisa_waktu='+sisa_waktu)
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
                        return fetch('{{route('ujian.selesai')}}?ujian_id='+ujian_id+'&question_id='+question_id+'&answer_id='+answer_id+'&sisa_waktu='+sisa_waktu)
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
                        window.location.replace("{{url('/')}}");
                    });
                    /*$.ajax({
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
                    });*/
                    
                });
            }
        }
        function getExams(url) {
            var nomor_soal = getUrlParameter(url);
            if(nomor_soal == 0){
                nomor_soal = parseInt(nomor_soal) + 1;
            }
            $('.name_text').html('SOAL NOMOR '+nomor_soal+' dari {{$ujian->question_count}} soal');
            $('#nomor_soal_mini').html(nomor_soal+' dari {{$ujian->question_count}} soal');
            var ujian_id = $('#ujian_id').val();
            var question_id = $('#question_id').val();
            var answer_id = $("input[name='answer_id']:checked").val();
            if(!answer_id){
                answer_id = 'kosong';
            } else {
                $('.'+question_id).removeClass('btn-warning').removeClass('btn-default').addClass("btn-success");
            }
            var sisa_waktu = $('#sisa_waktu').val();
            var ragu = $('#ragu').val();
            if(ragu){
                $('.'+question_id).removeClass('btn-success').removeClass('btn-default').addClass("btn-warning");
            }
            var kunci = [];
            $('input[name=kunci]').each(function(k,v){
                kunci.push(this.value);
            });
            $.ajax({
                url : url,
                data: {ujian_id:ujian_id, question_id:question_id, answer_id:answer_id, sisa_waktu:sisa_waktu,ragu:ragu, keys:kunci}
            }).done(function (response) {
                if(typeof response =='object'){
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
                    if(response ===false){
                        // the response was a string "false", parseJSON will convert it to boolean false
                        Swal.fire({
                            icon: 'error',
                            text: 'Server tidak merespon. Silahkan refresh halaman ini!',
                            confirmButtonText: 'Refresh',
                            allowOutsideClick: false,
                        }).then(function(e) {
                            //window.location.replace(window.location.href);
                            getExams(url);
                        });
                    } else {
                        $('#load').html(response);
                        $('.loader').hide();
                        checkPilihan();
                    }
                }
            }).fail(function () {
                //alert('Articles could not be loaded.');
                Swal.fire({
                    icon: 'error',
                    text: 'Server tidak merespon. Silahkan refresh halaman ini!',
                    confirmButtonText: 'Refresh',
                    allowOutsideClick: false,
                }).then(function(e) {
                    //window.location.replace(window.location.href);
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
            selesaiUjian(2, ujian_id, question_id, answer_id, sisa_waktu);
        });
    });    
</script>
@endsection