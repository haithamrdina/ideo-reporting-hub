<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
       aria-label="Open user menu">
                        <span class="avatar avatar-sm bg-dark"
                              style="background-image: url({{global_asset('static/avatars/avatar.png')}})"></span>
        <div class="d-none d-xl-block ps-2">
            <div class="text-red">{{  Str::ucfirst(Auth::guard('user')->user()->lastname)}}&nbsp;{{  Str::ucfirst(Auth::guard('user')->user()->firstname)}}</div>
            <div class="mt-1 small text-muted">{{ Auth::guard('user')->user()->role->description() }}</div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow bg-red">
        <a class="dropdown-item"
           href="{{ route('tenant.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-fw fa-power-off text-red"></i>
            Log Out
        </a>
        <form id="logout-form" action="{{ route('tenant.logout') }}" method="POST" style="display: none;">
           @csrf
        </form>

    </div>
</div>
