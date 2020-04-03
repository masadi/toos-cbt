@extends('dashboard.modal')
@section('title')
Status Download
@endsection
@section('content')
<table class="table table-responsive-sm mb-0" style="margin-top:10px;">
    @foreach ($sinkron['server'] as $nama => $item)
    <tr>
        <td>DATA {{$loop->iteration}}</td>
        <td class="text-center">{{$item}}</td>
        <td class="text-center">-</td>
        <td class="text-center">{{$sinkron['local'][$nama]}}</td>
    </tr>
    @endforeach
</table>
@endsection