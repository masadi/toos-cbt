@extends('dashboard.modal')
@section('title')
{{$title}}
@endsection
@section('content')
<table class="table table-responsive-sm table-bordered">
    <tr>
        <td colspan="2">{!!$data->soal!!}</td>
    </tr>
    @forelse ($data->jawaban as $key => $item)
    <tr>
        <td>{{Helper::generateAlphabet($key)}}. {!!$item->jawaban!!}</td>
        <td>{{($item->benar) ? 'Benar' : 'Salah'}}</td>
    </tr> 
    @empty
    <tr>
        <td colspan="2">Tidak ada jawaban</td>
    </tr>
    @endforelse
</table>
@endsection