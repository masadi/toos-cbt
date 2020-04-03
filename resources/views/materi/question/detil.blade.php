@extends('dashboard.modal')
@section('title')
{{$title}}
@endsection
@section('content')
<table class="table table-responsive-sm table-bordered">
    <tr>
        <td colspan="2">{!!$data->question!!}</td>
    </tr>
    @forelse ($data->answer as $key => $item)
    <tr>
        <td>{{Helper::generateAlphabet($key)}}. {!!$item->answer!!}</td>
        <td>{{($item->correct) ? 'Benar' : 'Salah'}}</td>
    </tr> 
    @empty
    <tr>
        <td colspan="2">Tidak ada jawaban</td>
    </tr>
    @endforelse
</table>
@endsection