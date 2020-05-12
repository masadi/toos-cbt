<table width="100%" style="margin-bottom: 0px; border-top:none;" class="border">
	<tr>
		<td width="35%">Nama Peserta</td>
		<td width="65%">: {{$anggota->nama_peserta_didik}}</td>
	</tr>
	<tr>
		<td>Program Keahlian</td>
		<td>: {{$anggota->rombongan_belajar->jurusan_sp->nama_jurusan_sp}}</td>
	</tr>
	<tr>
		<td>Username</td>
		<td>: {{$anggota->peserta_didik->user->username}}</td>
	</tr>
	<tr>
		<td>Password</td>
		<td>: {{$anggota->peserta_didik->user->checkPassword()}}</td>
	</tr>
</table>