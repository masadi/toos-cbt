@extends('adminlte::register')
@section('plugins.Moment', true)
@section('js')
<script>
    $('#tz').val(moment.tz.guess());
</script>
@endsection