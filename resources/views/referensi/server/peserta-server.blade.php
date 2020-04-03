<input type="hidden" name="server_id" id="server_id" value="{{$server_id}}">
<table class="table table-responsive-sm table-hover">
    <thead>
        <tr>
            <td class="text-center">No</td>
            <td>Nama Peserta Didik</td>
            <td class="text-center">Status</td>
        </tr>
    </thead>
    <tbody>
        <input type="hidden" name="rombongan_belajar_id" value="{{$rombongan_belajar_id}}">
        @forelse ($anggota_rombel as $anggota)
        <tr>
            <td class="text-center">{{$loop->iteration}}</td>
            <td>{{strtoupper($anggota->peserta_didik->nama)}}</td>
            <td class="text-center">
                <label class="c-switch c-switch-label c-switch-success">
                    <input name="status[]" value="{{$anggota->anggota_rombel_id}}" class="c-switch-input"
                        type="checkbox" {{($anggota->server_id == $server_id) ? 'checked=""' : ''}}><span
                        class="c-switch-slider" data-checked="&#x2713" data-unchecked="&#x2715"></span>
                </label>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center">Tidak ada data untuk ditampilkan</td>
        </tr>
        @endforelse
    </tbody>
</table>