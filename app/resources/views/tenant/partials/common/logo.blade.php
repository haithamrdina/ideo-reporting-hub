@php
    $route = Auth::guard('user')->user()->role->logo();
@endphp
<a href="{{ route($route) }}">
    <img src="{{global_asset('static/logo/logo-app.svg')}}" width="110" height="32" alt="Ideo Reporting" class="navbar-brand-image">
</a>
