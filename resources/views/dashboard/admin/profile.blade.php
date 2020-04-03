@extends('dashboard.base')
@section('content')
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ __('Prole') }} {{ $user->name }}</div>
                    <div class="card-body">
                        <br>
                        @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                        @endif
                        @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                        @endif
                        @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            @foreach ($errors->all() as $error)
                            {{ $error }}<br />
                            @endforeach
                        </div>
                        @endif
                        <form method="POST" action="{{ route('users.update_data', ['id' => $user->user_id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            {{--@method('PUT')--}}
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <svg class="c-icon c-icon-sm">
                                                    <use
                                                        xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-user">
                                                    </use>
                                                </svg>
                                            </span>
                                        </div>
                                        <input class="form-control" type="text" placeholder="{{ __('Name') }}"
                                            name="name" value="{{ $user->name }}" required autofocus>
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">@</span>
                                        </div>
                                        <input class="form-control" type="text" placeholder="{{ __('E-Mail Address') }}"
                                            name="email" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <svg class="c-icon c-icon-sm">
                                                    <use
                                                        xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-lock-unlocked">
                                                    </use>
                                                </svg>
                                            </span>
                                        </div>
                                        <input class="form-control" type="password"
                                            placeholder="{{ __('Password lama (di isi jika ingin merubah password)') }}"
                                            name="current_password" value="">
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <svg class="c-icon c-icon-sm">
                                                    <use
                                                        xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-lock-locked">
                                                    </use>
                                                </svg>
                                            </span>
                                        </div>
                                        <input class="form-control" type="password"
                                            placeholder="{{ __('Password baru') }}" name="new_password" value="">
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <svg class="c-icon c-icon-sm">
                                                    <use
                                                        xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-lock-locked">
                                                    </use>
                                                </svg>
                                            </span>
                                        </div>
                                        <input class="form-control" type="password"
                                            placeholder="{{ __('Konfirmasi password baru') }}"
                                            name="new_confirm_password" value="">
                                    </div>
                                </div>
                                <div class="col-sm-4 text-center">
                                    @if($user->photo)
                                    <img src="{{ env('APP_URL') }}/storage/uploads/profile/200/{{$user->photo}}"
                                        alt="{{$user->name}}">
                                    @else
                                    <img src="{{ env('APP_URL') }}/assets/img/avatars/{{($user->peserta_didik->jenis_kelamin == 'L') ? 'male' : 'female' }}-lg.png"
                                        alt="{{$user->name}}">
                                    @endif
                                    <input type="file" name="file" class="form-control" value="">
                                </div>
                            </div>
                            <button class="btn btn-success" type="submit">{{ __('Simpan') }}</button>
                            <a href="{{ url('/') }}" class="btn btn-primary">{{ __('Kembali') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
@endsection