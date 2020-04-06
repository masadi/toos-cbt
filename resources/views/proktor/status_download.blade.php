@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        Status Download
                    </div>
                    <div class="card-body">
                        <button class="btn btn-secondary start_download has-spinner" data-text="DOWNLOADING...">START DOWNLOAD</button>
                        <button class="btn btn-primary refresh_status">REFRESH STATUS</button>
                        <table class="table table-responsive-sm table-outline mb-0" style="margin-top:10px;">
                            <thead class="thead-light">
                                <tr>
                                    <th colspan="3">Status Progress Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sinkron as $nama => $item)
                                <tr>
                                    <td width="5%" class="text-center">{{$loop->iteration}}</td>
                                    <td width="85%">
                                        DATA {{$loop->iteration}}<br>
                                        <div class="progress mb-3" style="height: 30px;">
                                            <div id="status-{{$nama}}" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td width="10%" id="jumlah-{{$nama}}" class="text-center">Kosong</td>
                                </tr>
                                @endforeach
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
<link href="{{ asset('assets/btn/buttonLoader.css') }}" rel="stylesheet" type="text/css"> 
@endsection
@section('javascript')
<script src="{{ asset('assets/btn/jquery.buttonLoader.js') }}"></script>
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
$(function() {
    $.get("{{route('proktor.index', ['query' => 'hitung-server'])}}", function(data, status){
        if(status === 'success'){
            /*var result = Object.keys(data.local).map(function (key) {
                return [key, data.local[key]]; 
            });*/
            var result = Object.entries(data.local);  
            var a = 0;
            $.each(data.server.data, function (i, item) {
                var persen = (result[a][1] / item)*(100);
                if(isFinite(persen)){
                    persen = persen.toFixed(2);
                } else {
                    persen = 0;
                }
                var jumlah = (persen) ? data.local[i]+'/'+item : 'Kosong';
                $('#status-'+i).attr('aria-valuenow', persen).css('width', persen+'%').html(persen+'%');
                $('#status-'+i).removeClass('progress-bar-animated');
                $('#status-'+i).removeClass('progress-bar-striped');
                $('#jumlah-'+i).html(jumlah);
                a++;
            });
        } else {
            Swal.fire({
                icon: 'error',
                text: 'Server tidak merespon. Silahkan refresh halaman ini!',
                confirmButtonText: 'Refresh'
            }).then(function(e) {
                window.location.replace(window.location.href);
            });
        }
    });
    $('.refresh_status').click(function(e){
        e.preventDefault();
        $.get("{{route('proktor.index', ['query' => 'get-status-download'])}}", function(data, status){
            if(status === 'success'){
                $('#modal_content').modal({backdrop: 'static', keyboard: false});
                $('#modal_content').html(data);
            } else {
                Swal.fire({
                    icon: 'error',
                    text: 'Cek status progress download gagal. Silahkan refresh halaman ini!',
                    confirmButtonText: 'Refresh'
                }).then(function(e) {
                    window.location.replace(window.location.href);
                });
            }
        });
    });
    $('#modal_content').on('hidden.coreui.modal', function(){
        $('.start_download').addClass('btn-secondary');
        $('.start_download').addClass('btn-danger');
    });
    function myTimer(query, length) {
        $.get("{{url('/hitung-data')}}/"+query+"/"+length, function(data, status){
            if(status === 'success'){
                $('#status-'+data.query).attr('aria-valuenow', data.percent).css('width', data.percent+'%').html(data.percent+'%');
                $('#status-'+data.query).addClass('progress-bar-animated');
                $('#status-'+data.query).addClass('progress-bar-striped');
                $('#jumlah-'+data.query).html(data.jumlah);
            }
        });
    }
    function proses_download(query, offset){
        $.get("{{url('/proses-download')}}/"+query+'/'+offset, function(data, status){
            if(status === 'success'){
                /*var chekArray = Array.isArray(data.response.data);
                var panjang;
                if(chekArray){
                    panjang = data.response.data.length;
                } else {
                    panjang = data.response.data;
                }*/
                if(data.response){
                    var myVar = setInterval(function(){
                        myTimer(query, data.response.count);
                    }, 500);
                    $.ajax({
                        url: '{{route('proktor.simpan', ['query' => 'sync'])}}',
                        type: 'post',
                        data: data,
                        success: function(response){
                            setTimeout(function(){
                                clearInterval(myVar);
                                myVar = 0;
                                if(data.response.next){
                                    console.log('next disini');
                                    proses_download(data.response.next, data.response.offset);
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        text: 'Progress download berhasil. Silahkan refresh halaman ini!',
                                        confirmButtonText: 'Refresh'
                                    }).then(function(e) {
                                        window.location.replace(window.location.href);
                                    });
                                }
                            }, 2000);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: data.message,
                        confirmButtonText: 'Refresh'
                    }).then(function(e) {
                        window.location.replace(window.location.href);
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    text: 'Progress download gagal. Silahkan refresh halaman ini!',
                    confirmButtonText: 'Refresh'
                }).then(function(e) {
                    window.location.replace(window.location.href);
                });
            }
        });
    }
    $('.start_download').click(function(e){
        e.preventDefault();
        if($(this).hasClass('btn-danger')){
            var btn = $(this);
            $(btn).buttonLoader('start', $(this).data('text'));
            proses_download('ptk', 0);
        } else {
            Swal.fire({
                icon: 'error',
                text: 'Silahkan klik tombol REFRESH STATUS terlebih dahulu',
            });
        }
    });
});
</script>
@endsection