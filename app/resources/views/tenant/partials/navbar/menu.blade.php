@php
    $menus = Auth::guard('user')->user()->role->menu();
@endphp

@foreach($menus as $menu)
<li class="nav-item  {{ request()->routeIs($menu['route']) ? 'active' : '' }}">
    <a class="nav-link" href="{{ $menu['route'] ? route($menu['route']) : '.' }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            {!! $menu['icon'] !!}
        </span>
        <span class="nav-link-title text-white">
            {{ $menu['name'] }}
        </span>
    </a>
</li>
@endforeach
