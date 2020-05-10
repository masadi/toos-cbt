@extends('layouts.modal')
@section('title')
Tambah Jadwal Ujian di Kelas {{$rombongan_belajar->nama}}
@endsection
@section('content')
<form id="insert_jadwal" class="form-horizontal">
    <input type="hidden" name="rombongan_belajar_id" value="{{$rombongan_belajar->rombongan_belajar_id}}">
    <div class="form-group row">
        <label for="pembelajaran_id" class="col-sm-2 col-form-label">Mata Pelajaran</label>
        <div class="col-sm-10">
            <select name="pembelajaran_id" id="pembelajaran_id" class="form-control select2">
                <option value="">== Pilih Pembelajaran ==</option>
                @foreach ($rombongan_belajar->pembelajaran as $pembelajaran)
                <option value="{{$pembelajaran->pembelajaran_id}}">{{$pembelajaran->nama_mata_pelajaran}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label for="date" class="col-sm-2 col-form-label">Tanggal</label>
        <div class="col-sm-10">
            <input type="text" class="form-control date start" id="date" name="date">
        </div>
    </div>
    <div class="form-group row">
        <label for="from" class="col-sm-2 col-form-label">Jam Mulai</label>
        <div class="col-sm-10">
            <input type="text" class="form-control time start" name="from" id="from" />
        </div>
    </div>
    <div class="form-group row">
        <label for="to" class="col-sm-2 col-form-label">Jam Berakhir</label>
        <div class="col-sm-10">
            <input type="text" class="form-control time end" name="to" id="to" />
            <input type="hidden" class="date end" />
        </div>
    </div>
</form>
@endsection
@section('footer')
<a class="simpan_jadwal btn btn-default float-right">Simpan</a>
@endsection
@section('plugins.Datepicker', true)
@section('plugins.Timepicker', true)
@section('plugins.Datepair', true)
@section('js')
<script>
$('.simpan_jadwal').click(function(){
    $.ajax({
        url: '{{route('proktor.simpan', ['query' => 'jadwal-ujian'])}}',
        type: 'post',
        data: $('#insert_jadwal').serialize(),
    }).done(function( data ) {
        Swal.fire({
            icon: data.icon,
            text: data.text,
            title: data.title,
        }).then(function(e) {
            $('#modal_content').modal('hide');
            $('#datatable').DataTable().ajax.reload( null, false );
        });
    }).fail(function(data) {
        console.log(data.responseJSON.errors);
        var errors = [];
        $.each(data.responseJSON.errors, function (i, item) {
            errors.push(item[0]);
        })
        console.log(errors)
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: errors.join('<br>'),
        });
    });
})
$('.select2').select2({theme:'bootstrap4'});
$('#insert_jadwal .time').timepicker({
		'showDuration': true,
        'show2400': true,
		'timeFormat': 'H:i'
	});

	$('#insert_jadwal .date').datepicker({
		'format': 'yyyy-mm-dd',
		'autoclose': true
	});

	// initialize datepair
	$('#insert_jadwal').datepair({
        defaultTimeDelta:7200000
    });
</script>
@endsection