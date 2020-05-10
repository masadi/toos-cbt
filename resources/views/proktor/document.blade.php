@extends('layouts.cetak')
@section('content')
<table width="100%" style="margin-bottom: 0px;">
	<tr class="border">
		<td width="20%"><img src="{{url('vendor/img/cyber_education.jpg')}}" border="0" width="50" /></td>
		<td width="60%" class="text-center">KARTU PESERTA <br> UJI KOMPETENSI KEAHLIAN <br> Tahun Pelajaran 2019/2020</td>
		<td width="20%" class="border text-center">PANITIA</td>
	</tr>
</table>
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
	<tr>
		<td colspan="2" class="px-2 py-2">
			<table width="100%" style="margin-bottom: 0px; border-top:none;border-bottom:none; border-left:none; border-color:white;" class="border">
			<tr>
				<td width="20%" class="text-center border" style="padding:40px 0px;">
				Foto 2x3		
				</td>
				<td style="padding-left:5px;">
					<table class="border" width="100%">
						<tr>
							<td class="border strong text-center">Hari, Tanggal</td>
							<td class="border strong text-center">Mata Pelajaran</td>
							<td class="border strong text-center">Jam</td>
						</tr>
						@foreach ($anggota->rombongan_belajar->jadwal as $jadwal)
						<tr>
							<td class="border text-center">{{Helper::nama_hari(date('D', strtotime($jadwal->tanggal)))}}, {{Helper::TanggalIndo($jadwal->tanggal)}}</td>
							<td class="border text-center">{{$jadwal->pembelajaran->nama_mata_pelajaran}}</td>
							<td class="border text-center">{{date('H:i', strtotime($jadwal->from))}}-{{date('H:i', strtotime($jadwal->to))}}</td>
						</tr>
						@endforeach
					</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
@endsection