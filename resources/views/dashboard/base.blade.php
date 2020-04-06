<?php
$user = auth()->user();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="TOOS, Tes Online Offline Sekolah">
    <meta name="author" content="CV. Cyber Electra">
    <meta name="keyword" content="TOOS, CBT, UNBK, Aplikasi Ujian">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TOOS V.3.x</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('assets/favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
    <!-- Icons-->
    <link href="{{ asset('css/free.min.css') }}" rel="stylesheet"> <!-- icons -->
    <link href="{{ asset('css/flag-icon.min.css') }}" rel="stylesheet"> <!-- icons -->
    <!-- Main styles for this application-->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pace.min.css') }}" rel="stylesheet">

    @yield('css')

    <link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
</head>

<body class="c-app">
    <div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show c-sidebar-minimized" id="sidebar">
        @include('dashboard.shared.nav-builder')
        @include('dashboard.shared.header')
        <div class="c-body">
            <main class="c-main">
                @yield('content')
            </main>
        </div>
        @include('dashboard.shared.footer')
    </div>
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <!-- CoreUI and necessary plugins-->
    <script src="{{ asset('js/pace.min.js') }}"></script>
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('javascript')
    @role('proktor')
    <?php
    $segmen = Request::segments();
    if($segmen){
        if($segmen[0] != 'status-download'){
    ?>
    <script>
        let detik = 1 * 60000;
        let intervalId = setInterval(callback, detik);
        function callback () {
            console.log(new Date());
            $.get("{{route('proktor.index', ['query' => 'check-job'])}}", function( data ) {
                $('#token_ujian').html(data);
            });
            clearInterval(intervalId);
            intervalId = setInterval(callback, detik);
        }
        $.get("{{route('proktor.index', ['query' => 'check-job'])}}", function( data ) {
            $('#token_ujian').html(data);
        });
    </script>
    <?php
        }
    }
    ?>
    @endrole
</body>

</html>