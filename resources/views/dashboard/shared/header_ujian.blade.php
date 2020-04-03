<div class="c-wrapper">
    <header class="c-header c-header-light c-header-fixed c-header-with-subheader bg-primary">
        <ul class="c-header-nav d-md-down-none">
            @if($user)
            <li class="c-header-nav-item px-3"><img style="margin-top:10px;" src="{{ asset('assets/img/logo.png') }}"
                    width="150" alt="TOOS"></li>
            @endif
            <li class="c-header-nav-item" style="color:white;">
                @if($ujian)
                <strong>Nomor Soal : <span id="nomor_soal">1</span></strong>
                @endif
            </li>
        </ul>
        <ul class="c-header-nav ml-auto mr-4">
            @if($user)
            <li class="c-header-nav-item" style="color:white;">
                <strong>Selamat Datang {{$user->name}}</strong>
            </li>
            @endif
            <li class="c-header-nav-item dropdown"><a class="c-header-nav-link" data-toggle="dropdown" href="#"
                    role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="c-avatar">
                        @if($user)
                        @if($user->photo)
                        <img class="c-avatar-img" src="{{ env('APP_URL') }}/storage/uploads/avatars/{{$user->photo}}"
                            alt="{{$user->name}}">
                        @else
                        @role('peserta_didik')
                        <img class="c-avatar-img"
                            src="{{ env('APP_URL') }}/assets/img/avatars/{{($user->peserta_didik->jenis_kelamin == 'L') ? 'male' : 'female' }}-md.png" alt="{{$user->name}}">
                        @else
                        <img class="c-avatar-img"
                            src="{{ env('APP_URL') }}/assets/img/avatars/male-md.png" alt="{{$user->name}}">
                        @endif
                        @endif
                        <br>
                        @else
                        <img class="c-avatar-img" src="{{ env('APP_URL') }}/assets/img/avatars/6.jpg"
                            alt="user@email.com">
                        @endif
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right pt-0">
                    <div class="dropdown-header bg-light py-2"><strong>Account</strong></div>
                    <a class="dropdown-item" href="/signout">
                        <svg class="c-icon mr-2">
                            <use
                                xlink:href="{{ env('APP_URL') }}/assets/icons/coreui/free-symbol-defs.svg#cui-account-logout">
                            </use>
                        </svg> Logout
                    </a>
                </div>
            </li>
        </ul>
        @if($ujian)
        <div class="c-subheader px-3 clearfix bg-primary" style="display:block; border:none;">
            <div class="sub_header float-left text-light">Mata Ujian : {{$ujian->nama}}</div>
            <div class="sub_header float-right text-light d-md-down-none">Sisa Waktu : <span id="clock"></span></div>
            <input type="hidden" id="sisa_waktu">
        </div>
        @endif
    </header>