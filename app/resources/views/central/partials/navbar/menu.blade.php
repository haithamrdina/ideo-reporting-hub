<li class="nav-item {{ request()->routeIs('admin.home') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.home') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="#C2181A" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
            </svg>
        </span>
        <span class="nav-link-title text-white">
            Tableau de bord
        </span>
    </a>
</li>
{{-- {{ request()->routeIs('clients.*') ? 'active' : '' }} --}}
{{-- {{ route('clients.index') }} --}}
<li class="nav-item ">
    <a class="nav-link" href="">
        <span  class="nav-link-icon d-md-none d-lg-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-binary-tree" width="32" height="32" viewBox="0 0 24 24" stroke-width="1" stroke="#C2181A" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M6 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"></path>
                <path d="M16 4a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"></path>
                <path d="M16 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"></path>
                <path d="M11 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"></path>
                <path d="M21 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"></path>
                <path d="M5.058 18.306l2.88 -4.606"></path>
                <path d="M10.061 10.303l2.877 -4.604"></path>
                <path d="M10.065 13.705l2.876 4.6"></path>
                <path d="M15.063 5.7l2.881 4.61"></path>
             </svg>
        </span>
        <span class="nav-link-title text-white">
            Clients
        </span>
    </a>
</li>
