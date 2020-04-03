@extends('dashboard.modal')
@section('title')
{{$title}}
@endsection
@section('content')
@if(!\Ramsey\Uuid\Uuid::isValid($data->rombongan_belajar_id))
<select name="rombongan_belajar_id" id="rombongan_belajar_id" class="form-control">
    <option value="">== Pilih Rombongan Belajar ==</option>
    @forelse ($data_satu as $item)
    <option value="{{$item->rombongan_belajar_id}}">{{$item->nama}}</option>
    @empty
    <option value="">Tidak ditemukan data Rombongan Belajar</option>
    @endforelse
</select>
<br>
@endif
<form id="form">
    <input type="hidden" name="server_id" id="server_id" value="{{$data->server_id}}">
    <table class="table table-responsive-sm table-hover">
        <thead>
            <tr>
                <td class="text-center">No</td>
                <td>Nama Peserta Didik</td>
                <td class="text-center">Status</td>
            </tr>
        </thead>
        <tbody>
            @if($data->rombongan_belajar)
            <input type="hidden" name="rombongan_belajar_id" value="{{$data->rombongan_belajar_id}}">
            @forelse ($data->rombongan_belajar->anggota_rombel as $anggota)
            @if(!$anggota->server_id || $anggota->server_id == $data->server_id)
            <tr>
                <td class="text-center">{{$loop->iteration}}</td>
                <td>{{strtoupper($anggota->peserta_didik->nama)}}</td>
                <td class="text-center">
                    <label class="c-switch c-switch-label c-switch-success">
                        <input name="status[]" value="{{$anggota->anggota_rombel_id}}" class="c-switch-input"
                            type="checkbox" {{($anggota->server_id == $data->server_id) ? 'checked=""' : ''}}><span
                            class="c-switch-slider" data-checked="&#x2713" data-unchecked="&#x2715"></span>
                    </label>
                </td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="3" class="text-center">Tidak ada data untuk ditampilkan</td>
            </tr>
            @endforelse
            @else
            <tr>
                <td colspan="3" class="text-center">Tidak ada data untuk ditampilkan</td>
            </tr>
            @endif
        </tbody>
    </table>
</form>
@endsection
@section('footer')
<button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
<a class="btn btn-primary" id="simpan_peserta_server" href="javascript:void(0)">Simpan</a>
@endsection
@section('js')
<script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script>
    $('#rombongan_belajar_id').change(function(){
        var ini = $(this).val();
        var server_id = $('#server_id').val();
        if(ini == ''){
            return false;
        } else {
            $.ajax({
                url: '{{route('ajax.get_data', ['query' => 'peserta-server'])}}',
                type: 'post',
                data: {rombongan_belajar_id:ini, server_id:server_id},
                success: function(response){
                    $("#form").html(response);
                }
            });
		}
    });
    $('#simpan_peserta_server').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{route('referensi.simpan', ['query' => 'peserta-server'])}}',
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