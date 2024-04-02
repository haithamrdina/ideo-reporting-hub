<div class="nav-item dropdown">
    <a href="javascript:void(0)" class="nav-link d-flex lh-1 btn btn-yellow text-dark d-sm-none btn-icon text-reset p-0 show"
        data-bs-toggle="dropdown" aria-label="Open user menu" aria-expanded="true">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments-alt" width="24"
            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
            stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M4 8h4v4h-4z"></path>
            <path d="M6 4l0 4"></path>
            <path d="M6 12l0 8"></path>
            <path d="M10 14h4v4h-4z"></path>
            <path d="M12 4l0 10"></path>
            <path d="M12 18l0 2"></path>
            <path d="M16 5h4v4h-4z"></path>
            <path d="M18 4l0 1"></path>
            <path d="M18 9l0 11"></path>
        </svg>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow bg-yellow" data-bs-popper="static">
        <h6 class="dropdown-header text-blue">OVERVIEW</h6>
        <a href="{{ route('admin.tenants.show',['tenant' => $tenant->id]) }}" class="dropdown-item">Tableau de bord</a>
        <h6 class="dropdown-header text-blue">UTILISATEURS</h6>
        <a href="{{ route('admin.tenants.projects',['tenant' => $tenant->id]) }}" class="dropdown-item">Projets</a>
        <a href="{{ route('admin.tenants.groups',['tenant' => $tenant->id]) }}" class="dropdown-item">Groupes</a>
        <a href="{{ route('admin.tenants.learners',['tenant' => $tenant->id]) }}" class="dropdown-item">Apprenants</a>
        <h6 class="dropdown-header text-blue">E-LEARNING</h6>
        <a href="{{ route('admin.tenants.lps',['tenant' => $tenant->id]) }}" class="dropdown-item">Plans de formation</a>
        <a href="#" class="dropdown-item">Modules</a>
        <a href="{{ route('admin.tenants.moocs',['tenant' => $tenant->id]) }}" class="dropdown-item">Mooc</a>
        <h6 class="dropdown-header text-blue">INSCRIPTIONS</h6>
        <a href="{{ route('admin.tenants.softskills.enroll',['tenant' => $tenant->id]) }}" class="dropdown-item">Softskills</a>
        <a href="{{ route('admin.tenants.digitals.enroll',['tenant' => $tenant->id]) }}" class="dropdown-item">Digital</a>
        <a href="{{ route('admin.tenants.langues.enroll',['tenant' => $tenant->id]) }}" class="dropdown-item">Langue</a>
        <a href="{{ route('admin.tenants.moocs.enroll',['tenant' => $tenant->id]) }}" class="dropdown-item">Mooc</a>
        <a href="{{ route('admin.tenants.lps.enroll',['tenant' => $tenant->id]) }}" class="dropdown-item">Plan de formation</a>
        <h6 class="dropdown-header text-blue">LEARNER SUCCESS CENTER</h6>
        <a href="{{ route('admin.tenants.calls',['tenant' => $tenant->id]) }}" class="dropdown-item">Appels téléphoniques</a>
        <a href="{{ route('admin.tenants.tickets',['tenant' => $tenant->id]) }}" class="dropdown-item">Tickets</a>
        <h6 class="dropdown-header text-blue">GAMIFICATION</h6>
        <a href="{{ route('admin.tenants.badges',['tenant' => $tenant->id]) }}" class="dropdown-item">Badges</a>
    </div>
</div>
<a href="{{ route('admin.tenants.index') }}" class="btn text-dark d-none d-sm-inline-block">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="32" height="32"
        viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round"
        stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M9 6l11 0"></path>
        <path d="M9 12l11 0"></path>
        <path d="M9 18l11 0"></path>
        <path d="M5 6l0 .01"></path>
        <path d="M5 12l0 .01"></path>
        <path d="M5 18l0 .01"></path>
    </svg>
    Tenants
</a>
<a href="{{ route('admin.tenants.index') }}" class="btn text-dark d-sm-none btn-icon" data-bs-toggle="tooltip"
    data-bs-placement="top" title="Tenants">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="32" height="32"
        viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round"
        stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M9 6l11 0"></path>
        <path d="M9 12l11 0"></path>
        <path d="M9 18l11 0"></path>
        <path d="M5 6l0 .01"></path>
        <path d="M5 12l0 .01"></path>
        <path d="M5 18l0 .01"></path>
    </svg>
</a>
<a href="{{ 'http://' . $tenant->domains->first()->domain . '.' .config('app.domain') }}" class="btn btn-red d-none d-sm-inline-block" target="_blank">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-app-window" width="32"
        height="32" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round"
        stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path>
        <path d="M6 8h.01"></path>
        <path d="M9 8h.01"></path>
    </svg>
    Visit Website
</a>
<a href="{{ 'http://' . $tenant->domains->first()->domain . '.' .config('app.domain') }}" class="btn btn-red d-sm-none btn-icon" data-bs-toggle="tooltip"
    data-bs-placement="top" title="Visit Website" target="_blank">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-app-window" width="32"
        height="32" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round"
        stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M3 5m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path>
        <path d="M6 8h.01"></path>
        <path d="M9 8h.01"></path>
    </svg>
</a>
