@if($item['text'] == 'timer')
<div class="timer text-white" style="display: none;"><b>Sisa Waktu : <span id="clock"></span></b></div>
<input type="hidden" id="sisa_waktu">
@else
    @if (isset($item['search']) && $item['search'])
        <form action="{{ $item['href'] }}" method="{{ $item['method'] }}" class="form-inline ml-2 mr-2">
            <div class="input-group">
                <input class="form-control form-control-navbar" type="search" name="{{ $item['input_name'] }}" placeholder="{{ $item['text'] }}" aria-label="{{ $item['aria-label'] ?? $item['text'] }}">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    @elseif (is_array($item) && !isset($item['header']))
    <li @if (isset($item['id'])) id="{{ $item['id'] }}" @endif class="nav-item {{ $item['top_nav_class'] }}">
            <a class="nav-link @if (isset($item['submenu']))dropdown-item dropdown-toggle @endif" href="{{ $item['href'] }}"
                @if (isset($item['submenu'])) data-toggle="dropdown" @endif
                @if (isset($item['target'])) target="{{ $item['target'] }}" @endif
                {!! $item['data-compiled'] ?? '' !!}
            >
            @if($item['text'] == 'sekolah')
        <h1 class="text-lg mt-n1"> {{(Auth::user()->sekolah) ? Auth::user()->sekolah->nama : Auth::user()->name}}</h1>
        @else
                <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>
                {{ $item['text'] }}

            @if (isset($item['label']))
                <span class="badge badge-{{ $item['label_color'] ?? 'primary' }}">{{ $item['label'] }}</span>
            @endif
            @endif
        </a>
        @if (isset($item['submenu']))
            <ul class="dropdown-menu border-0 shadow">
                @each('adminlte::partials.menuitems.menu-item-sub-top-nav', $item['submenu'], 'item')
            </ul>
        @endif
    </li>
    @endif
@endif
