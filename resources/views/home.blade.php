@extends('adminlte::page')

@section('title', 'TOOS CBT V.3.x')

@section('content_header')
<h1 class="m-0 text-dark">Dashboard</h1>
@stop
@section('content')
<?php
$user = Auth::user();
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if(session('login-success'))
                <div class="alert alert-success" role="alert">
                    {{ session('login-success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
                @endif
                @role('proktor')
                <h2 class="text-success"><strong>AKTIF</strong></h2>
                <div class="alert alert-success" role="alert">Aplikasi siap digunakan</div>
                @endrole
                @role('peserta_didik')
                <div class="row">
                    <div class="col-md-2">
                        @if($user->photo)
                        <img src="{{ env('APP_URL') }}/storage/uploads/profile/200/{{$user->photo}}"
                            alt="{{$user->name}}">
                        @else
                        <img src="{{ env('APP_URL') }}/vendor/img/avatars/{{($user->peserta_didik->jenis_kelamin == 'L') ? 'male' : 'female' }}-md.png"
                            alt="{{$user->name}}">
                        @endif
                    </div>
                    <div class="col-md-5">
                        <table class="table table-responsive-sm table-sm">
                            <tr>
                                <td>Nama</td>
                                <td>: {{strtoupper($user->name)}}</td>
                            </tr>
                            <tr>
                                <td>NISN</td>
                                <td>: {{strtoupper($user->peserta_didik->nisn)}}</td>
                            </tr>
                            <tr>
                                <td>Tempat, Tanggal Lahir</td>
                                <td>: {{strtoupper($user->peserta_didik->tempat_lahir)}},
                                    {{Helper::TanggalIndo($user->peserta_didik->tanggal_lahir)}}</td>
                            </tr>
                            <tr>
                                <td>Rombongan Belajar</td>
                                <td>:
                                    {{strtoupper($user->peserta_didik->anggota_rombel->rombongan_belajar->nama)}}
                                </td>
                            </tr>
                            <!--tr>
                                <td>No WA Aktif</td>
                                <td class="wa_aktif">: {!!($user->phone_number) ? $user->routeNotificationForWhatsApp().' <a title="Perbaharui Nomor WhatsApp"
                                    href="'.route('ujian.update_pengguna').'"
                                    class="btn btn-sm btn-danger no_wa"><i class="fas fa-pencil-alt"></i></a>' : '<a
                                        href="'.route('ujian.update_pengguna').'"
                                        class="btn btn-sm btn-danger no_wa">Update No WhatsApp</a>'!!}
                                </td>
                            </tr-->
                        </table>
                    </div>
                    <div class="col-md-5">
                        <div class="font-lg"><strong>Informasi Aplikasi</strong></div>
                        <div>Nama Aplikasi : {{config('global.app_name')}}</div>
                        <div>Versi Aplikasi : {{config('global.app_version')}}</div>
                        <div>Versi Database : {{config('global.db_version')}}</div>
                        <div>Periode Aktif : {{Helper::semester()}}</div>
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
</div>
@role('peserta_didik')
<div class="row">
    @forelse ($exams as $exam)
    <div class="col-md-4">
        <!-- Widget: user widget style 2 -->
        <div class="card card-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-warning">
                <h3 class="widget-user-username" style="margin-left: 0px;">{{$exam->nama}}</h3>
                <h5 class="widget-user-desc" style="margin-left: 0px;">{{$exam->pembelajaran->nama_mata_pelajaran}}</h5>
            </div>
            <div class="card-footer p-0">
                <?php
                $today = strtotime(date('Y-m-d'));
                $tanggal = strtotime($exam->jadwal->tanggal);
                $current_time = strtotime(date('H:i:s'));
                $from = strtotime($exam->jadwal->from);
                $to = strtotime($exam->jadwal->to);
                ?>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Jumlah Soal <span class="float-right badge bg-primary">{{$exam->question_count}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Tanggal <span
                                class="float-right badge bg-info">{{Helper::tanggalIndo($exam->jadwal->tanggal)}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Jam Awal <span class="float-right badge bg-success">{{$exam->jadwal->from}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Jam Akhir <span class="float-right badge bg-danger">{{$exam->jadwal->to}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Jam Sekarang <span class="float-right badge bg-danger">{{date('H:i:s')}}</span>
                        </a>
                    </li>
                    @if($exam->user_exam)
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Check Ujian <span class="float-right badge bg-danger">{{$exam->user_exam->status_ujian}}</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        @if($tanggal == $today)
                        @if ($current_time > $from && $current_time < $to) 
                            @if($exam->user_exam)
                                @if($exam->user_exam->status_ujian)
                                <a href="{{route('ujian.proses', ['ujian_id' => $exam->exam_id])}}"
                                    class="nav-link btn btn-success btn-block btn-flat">
                                    Lanjutkan Ujian
                                </a>
                                @else
                                <a href="javascript:void(0)" class="btn btn-success btn-block btn-flat disabled">
                                    Ujian Selesai
                                </a>
                                @endif
                            @else
                            <a href="{{route('ujian.proses', ['ujian_id' => $exam->exam_id])}}"
                                class="nav-link btn btn-success btn-block btn-flat">
                                Mulai Ujian
                            </a>
                            @endif
                            @elseif($current_time < $to) <a href="javascript:void(0)"
                                class="btn btn-success btn-block btn-flat disabled">
                                Belum Mulai
                                </a>
                                @else
                                <a href="javascript:void(0)" class="btn btn-success btn-block btn-flat disabled">
                                    Waktu Habis
                                </a>
                                @endif
                                @elseif($tanggal > $today)
                                <a href="javascript:void(0)" class="btn btn-success btn-block btn-flat disabled">
                                    Belum Mulai
                                </a>
                                @else
                                <a href="javascript:void(0)" class="btn btn-success btn-block btn-flat disabled">
                                    Waktu Habis
                                </a>
                                @endif
                    </li>
                </ul>
            </div>
        </div>
        <!-- /.widget-user -->
    </div>
    @empty
    @endforelse
</div>
@endrole
@stop
@section('plugins.Sweetalert2', true)
@section('js')
<script>
    $('.no_wa').click(function(e){
        e.preventDefault();
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        Swal.fire({
            title: 'Masukkan nomor WhatsApp Aktif',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            showLoaderOnConfirm: true,
            preConfirm: (phone_number) => {
                return fetch($(this).attr('href'), {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                    },
                    body:JSON.stringify({
                        user_id: '{{$user->user_id}}',
                        phone_number : phone_number
                    })
                }).then(response => {
                    return response.json()
                }).catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            console.log(result);
            var text = [];
            if (result.value) {
                if(result.value.errors){
                    $.each(result.value.errors, function(i, item){
                        text.push(item[0]);
                    });
                    Swal.fire({
                        title: 'Gagal',
                        text: text.join(', '),
                        icon: 'error'
                    })
                } else {
                    Swal.fire({
                        title: result.value.title,
                        text: result.value.text,
                        icon: result.value.icon
                    }).then(function(){
                        if(result.value.icon == 'success'){
                            $('.wa_aktif').html(result.value.phone_number);
                        }
                    })
                }
            }
        })
    });
</script>
@endsection