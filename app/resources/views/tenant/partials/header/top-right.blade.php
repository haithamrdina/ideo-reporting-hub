<div class="d-none d-md-flex">
    <div class="nav-item dropdown d-none d-md-flex me-3" id="notification_div">
        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="red" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"></path>
                <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
            </svg>
            @if (count(Auth::guard('user')->user()->unreadNotifications) > 0)
                <span
                    class="badge bg-red badge-pill">{{ count(Auth::guard('user')->user()->unreadNotifications) }}</span>
            @else
                <span class="badge bg-red badge-pill">0</span>
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Notifications</h3>
                </div>
                <div class="list-group list-group-flush list-group-hoverable">
                    @if (count(Auth::guard('user')->user()->unreadNotifications) > 0)
                        @foreach (Auth::guard('user')->user()->unreadNotifications as $notification)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="">
                                        <div class="d-block text-secondary text-truncate mt-n1">
                                            {{ $notification->data['message'] }}
                                        </div>
                                        <a href="{{ $notification->data['link'] }}"
                                            class="text-red d-block download-excel"
                                            data-notification-id="{{ $notification->id }}" download="your_report.xlsx"
                                            onclick="markNotificationAsRead(event)">
                                            Télécharger votre rapport
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="empty-img">
                                        <img src="{{ global_asset('static/illustrations/no-data-found.svg') }}"
                                            class="w-100" height="128" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
        <span class="avatar avatar-sm bg-dark"
            style="background-image: url({{ global_asset('static/avatars/avatar.png') }})"></span>
        <div class="d-none d-xl-block ps-2">
            <div class="text-red">
                {{ Str::ucfirst(Auth::guard('user')->user()->lastname) }}&nbsp;{{ Str::ucfirst(Auth::guard('user')->user()->firstname) }}
            </div>
            <div class="mt-1 small text-muted">{{ Auth::guard('user')->user()->role->description() }}</div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow bg-red">
        <a class="dropdown-item" href="{{ route('tenant.logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-fw fa-power-off text-red"></i>
            Log Out
        </a>
        <form id="logout-form" action="{{ route('tenant.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

    </div>
</div>
