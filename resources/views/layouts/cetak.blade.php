<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Cetak Dokumen</title>
    <!-- Styles -->
	<link href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css')}}" rel="stylesheet">
    <link href="{{ asset('vendor/cetak/cetak.css') }}" rel="stylesheet">
</head>
<body>
	@yield('content')
</body>
</html>
