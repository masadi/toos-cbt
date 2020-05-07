<?php
$current_index = ($keys) ? array_search($current_id, $keys) : 0;
$count = ($keys) ? count($keys) : 0;
$next = $current_index + 1;
$prev = $current_index - 1;
?>
<div class="row clearfix">
    <?php if ($prev >= 0): ?>
        <a class="navigasi btn btn-lg btn-primary float-left" href="{{route('ujian.get_soal', ['page' => ($prev + 1),'soal_id' => $keys[$prev]])}}">Previous</a>
    <?php endif; ?>
    <div class="col text-center">
        <input type="hidden" id="ragu" value="">
        <input type="checkbox" id="ragu_button">
    </div>
    @if ($next < $count)
        <a class="navigasi btn btn-lg btn-primary float-right" href="{{route('ujian.get_soal', ['page' => ($next + 1),'soal_id' => $keys[$next]])}}">Next</a>
    @else
        @if($jumlah_jawaban_siswa >= ($count - 1))
        <a class="selesai btn btn-lg btn-danger float-right" href="javascript:void(0)">Selesai</a>
        @endif
    @endif
</div>
<?php
/*
@if ($paginator->hasPages())
<div class="row clearfix">
    @if ($paginator->onFirstPage())
    <a class="btn btn-lg btn-primary float-left" disabled="">@lang('pagination.previous')</a>
    @else
    <a class="navigasi btn btn-lg btn-primary float-left" href="{{ $paginator->previousPageUrl() }}"
        rel="prev">@lang('pagination.previous')</a>
    @endif
    <div class="col text-center">
        <input type="hidden" id="ragu" value="">
        <input type="checkbox" id="ragu_button">
    </div>
    @if ($paginator->hasMorePages())
    <a class="navigasi btn btn-lg btn-primary float-right" href="{{ $paginator->nextPageUrl() }}"
        rel="next">@lang('pagination.next')</a>
    @else
    @if($jumlah_jawaban_siswa >= ($paginator->total() - 1))
    <a class="selesai btn btn-lg btn-danger float-right" href="javascript:void(0)">Selesai</a>
    @endif
    @endif
</div>
@endif
*/
?>