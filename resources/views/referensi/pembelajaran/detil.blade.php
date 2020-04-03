@extends('dashboard.modal')
@section('title')
{{$title}}
@endsection
@section('content')
<form id="form">
    <input type="hidden" name="sekolah_id" value="{{$data_satu->sekolah_id}}">
    <input type="hidden" name="semester_id" value="{{$data_satu->semester_id}}">
    <input type="hidden" name="rombongan_belajar_id" value="{{$data_satu->rombongan_belajar_id}}">
    <table class="table table-responsive-sm table-hover">
        <thead>
            <tr>
                <td>No</td>
                <td>Mata Pelajaran</td>
                <td>PTK</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$item->mata_pelajaran->nama}}</td>
                <td>
                    <select name="ptk_id[]" id="ptk_id" class="form-control">
                        <option value="">== Pilih ==</option>
                        @foreach ($data_dua as $addon)
                        <option value="{{$addon->ptk_id}}"
                        @if($item->pembelajaran)
                        @if($addon->ptk_id == $item->pembelajaran->ptk_id)
                        selected
                        @endif
                        @else
                        @endif
                        >{{strtoupper($addon->nama)}}
                        </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="mata_pelajaran_id[]" value="{{$item->mata_pelajaran_id}}">
                    <input type="hidden" name="nama_mata_pelajaran[]" value="{{$item->mata_pelajaran->nama}}">
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Tidak ada data untuk ditampilkan. Silahkan menghubungi Administrator<br>{{$data_satu->tingkat_pendidikan_id}} : {{$data_satu->kurikulum_id}}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</form>
@endsection
@section('footer')
<button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
<a class="btn btn-primary" id="simpan_pembelajaran" href="javascript:void(0)">Simpan</a>
@endsection
@section('js')
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
    $('#simpan_pembelajaran').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{route('referensi.simpan', ['query' => 'pembelajaran'])}}',
            type: 'post',
            data: $("#form").serialize(),
            success: function(data){
                console.log('test');
                Swal.fire({
                    icon: data.icon,
                    text: data.status,
                }).then(function(e) {
                    $('#modal_content').modal('hide');
                });
            }
        });
    });
</script>
@endsection