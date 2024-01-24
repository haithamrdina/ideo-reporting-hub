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
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.learners') ? 'active' : '' }}">
            Apprenants
        </a>
    </div>
    <h4 class="subheader mt-4">E-LEARNING</h4>
    <div class="list-group list-group-transparent">
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.lps') ? 'active' : '' }}">
            Plans de formation
        </a>
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.modules') ? 'active' : '' }}">
            Modules
        </a>
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('admin.tenants.moocs') ? 'active' : '' }}">
            Mooc
        </a>
    </div>
    <h4 class="subheader mt-4">INSCRIPTIONS</h4>
    <div class="list-group list-group-transparent">
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
            Plans de formation
        </a>
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
            Softskills
        </a>
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
            Digital
        </a>
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
            Langue
        </a>
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
            Mooc
        </a>
    </div>
    <h4 class="subheader mt-4">LEARNER SUCCESS CENTER</h4>
    <div class="list-group list-group-transparent">
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
            Appels téléphoniques
        </a>
        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
            Tickets
        </a>
    </div>
</div>
