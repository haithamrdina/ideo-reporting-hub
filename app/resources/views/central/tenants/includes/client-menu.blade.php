<div class="card-body">
    <h4 class="subheader">OVERVIEW</h4>
    <div class="list-group list-group-transparent">
        <a href="{{ route('admin.tenants.show',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.show') ? 'active' : '' }}">
            Tableau de bord
        </a>
    </div>
    <h4 class="subheader mt-4">Utilisateurs</h4>
    <div class="list-group list-group-transparent">
        <a href="{{ route('admin.tenants.projects',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.projects') || request()->routeIs('admin.tenants.projects.*') ? 'active' : '' }}">
            Projets
        </a>
        <a href="{{ route('admin.tenants.groups',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.groups') ? 'active' : '' }}">
            Groupes
        </a>
        <a href="{{ route('admin.tenants.learners',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.learners') ? 'active' : '' }}">
            Apprenants
        </a>
    </div>
    <h4 class="subheader mt-4">E-LEARNING</h4>
    <div class="list-group list-group-transparent">
        <a href="{{ route('admin.tenants.lps',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.lps') ? 'active' : '' }}">
            Plans de formation
        </a>
        <a href="{{ route('admin.tenants.modules',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.modules') ? 'active' : '' }}">
            Modules
        </a>
        <a href="{{ route('admin.tenants.moocs',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.moocs') ? 'active' : '' }}">
            Mooc
        </a>
    </div>
    <h4 class="subheader mt-4">INSCRIPTIONS</h4>
    <div class="list-group list-group-transparent">
        <a href="{{ route('admin.tenants.lps.enroll',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.lps.enroll') ? 'active' : '' }}">
            Plans de formation
        </a>
        <a href="{{ route('admin.tenants.softskills.enroll',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.softskills.enroll') ? 'active' : '' }}">
            Softskills
        </a>
        <a href="{{ route('admin.tenants.digitals.enroll',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.digitals.enroll') ? 'active' : '' }}">
            Digital
        </a>
        <a href="{{ route('admin.tenants.langues.enroll',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.langues.enroll') ? 'active' : '' }}">
            Langue
        </a>
        <a href="{{ route('admin.tenants.moocs.enroll',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.moocs.enroll') ? 'active' : '' }}">
            Mooc
        </a>
    </div>
    <h4 class="subheader mt-4">LEARNER SUCCESS CENTER</h4>
    <div class="list-group list-group-transparent">
        <a href="{{ route('admin.tenants.calls',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.calls') ? 'active' : '' }}">
            Appels téléphoniques
        </a>
        <a href="{{ route('admin.tenants.tickets',['tenant' => $tenant->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.tickets') ? 'active' : '' }}">
            Tickets
        </a>
    </div>
</div>
