@extends('layouts.modal')
@section('title')
Edit Jadwal Ujian {{$jadwal->exam->nama}} Mata Pelajaran {{$jadwal->exam->pembelajaran->nama_mata_pelajaran}}
@endsection
@section('content')
<form id="insert_jadwal" class="form-horizontal">
    <input type="hidden" name="rombongan_belajar_id" value="{{$rombongan_belajar->rombongan_belajar_id}}">
    <input type="hidden" name="jadwal_id" value="{{$jadwal->id}}">
    <input type="hidden" name="pembelajaran_id" value="{{$jadwal->pembelajaran_id}}">
    <div class="form-group row">
        <label for="date" class="col-sm-2 col-form-label">Tanggal</label>
        <div class="col-sm-10">
            <input type="text" class="form-control date start" id="date" name="date" value="{{$jadwal->tanggal}}">
        </div>
    </div>
    <div class="form-group row">
        <label for="from" class="col-sm-2 col-form-label">Jam Mulai</label>
        <div class="col-sm-10">
            <input type="text" class="form-control time start" name="from" id="from" value="{{$jadwal->from}}" />
        </div>
    </div>
    <div class="form-group row">
        <label for="to" class="col-sm-2 col-form-label">Jam Berakhir</label>
        <div class="col-sm-10">
            <input type="text" class="form-control time end" name="to" id="to" value="{{$jadwal->to}}" />
            <input type="hidden" class="date end" />
        </div>
    </div>
</form>
@endsection
@section('footer')
<a class="simpan_jadwal btn btn-default float-right">Update</a>
@endsection
@section('plugins.Datepicker', true)
@section('plugins.Timepicker', true)
@section('plugins.Datepair', true)
@section('js')
<script>
$('.simpan_jadwal').click(function(){
    $.ajax({
        url: '{{route('proktor.simpan', ['query' => 'update-jadwal-ujian'])}}',
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