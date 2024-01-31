@extends('master')
@section('title')
    Tenants
@stop
@section('ideoreport_css')
@stop
@section('header')
    @include('central.partials.navbar.overlap-topbar')
@stop
@section('page-header')
    <div class="page-header d-print-none text-white">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Gestion des tenants
                    </div>
                    <h2 class="page-title">
                        Liste des tenants
                    </h2>
                </div>
                @if (count($tenants) > 0)
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="{{ route('admin.tenants.create') }}" class="btn btn-red d-none d-sm-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                Créer un nouveau tenant
                            </a>
                            <a href="{{ route('admin.tenants.create') }}" class="btn btn-red d-sm-none btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Créer un nouveau tenant">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif
                <div class="col-auto ms-auto d-print-none">
                    @include('central.partials.common.alert')
                </div>
            </div>
        </div>
    </div>
@stop
@section('page-content')
    @if (count($tenants) > 0)
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-lg-12">
                    <div class="row row-cards">
                        <div class="col-12">
                            <div class="card">
                                <div id="table-tenants" class="table-responsive">
                                    <div class="card-body border-bottom py-3">
                                        <div class="d-flex">
                                            <div class="text-muted">
                                                Show
                                                <div class="mx-2 d-inline-block">
                                                    <div class="mx-2 d-inline-block">
                                                        <select class="form-select h-5" id="listjs-items-per-page">
                                                            <option value="10" selected>10</option>
                                                            <option value="25">25</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                entries
                                            </div>
                                            <div class="ms-auto text-muted">
                                                Search:
                                                <div class="ms-2 d-inline-block">
                                                    <input type="text" class="form-control form-control-sm search">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table card-table table-vcenter text-nowrap datatable">
                                            <thead>
                                                <tr>
                                                    <th><button class="table-sort" data-sort="sort-doceboidorg">Docebo ORG_ID</button></th>
                                                    <th><button class="table-sort" data-sort="sort-zendeskidorg">ZENDESK ORG_ID</button></th>
                                                    <th><button class="table-sort" data-sort="sort-code">Code</button></th>
                                                    <th><button class="table-sort" data-sort="sort-name">Nom</button></th>
                                                    <th><button class="table-sort" data-sort="sort-domain">Domain</button></th>
                                                    <th><button class="table-sort" data-sort="sort-created">Date de création</button></th>
                                                    <th><button class="table-sort" data-sort="sort-updated">Date de modification</button></th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-tbody">
                                                @if(count($tenants) > 0)
                                                    @foreach($tenants as $tenant)
                                                    <tr>
                                                        <td class="sort-doceboidorg">
                                                            {{$tenant->docebo_org_id}}
                                                        </td>
                                                        <td class="sort-zendeskidorg">
                                                            {{$tenant->zendesk_org_id}}
                                                        </td>
                                                        <td class="sort-logo">
                                                            {{$tenant->company_code}}
                                                        </td>
                                                        <td class="sort-name">
                                                            {{$tenant->company_name}}
                                                        </td>
                                                        @production
                                                            <td class="sort-domain">
                                                                <a href="{{ 'https://' . $tenant->domains->first()->domain }}" target="_blank">
                                                                    <span class="badge badge-outline text-red">{{ 'https://' . $tenant->domains->first()->domain }}</span>
                                                                </a>
                                                            </td>
                                                        @else
                                                            <td class="sort-domain">
                                                                <a href="{{ 'http://' . $tenant->domains->first()->domain . '.' .config('app.domain') }}" target="_blank">
                                                                    <span class="badge badge-outline text-red">{{ 'http://' . $tenant->domains->first()->domain . '.' . config('app.domain') }}</span>
                                                                </a>
                                                            </td>
                                                        @endproduction

                                                        <td class="sort-created" data-created="{{ date('U', strtotime($tenant->created_at)) }}">
                                                            {{ date('d-m-Y H:i:s', strtotime($tenant->created_at)) }}
                                                        </td>
                                                        <td class="sort-created" data-created="{{ date('U', strtotime($tenant->updated_at)) }}">
                                                            {{ date('d-m-Y H:i:s', strtotime($tenant->updated_at)) }}
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="btn btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Gérer votre tenant">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments-alt" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                                                            <a href="{{ route('admin.tenants.destroy', $tenant->id) }}" class="btn btn-danger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer ce tenant" onclick="event.preventDefault(); document.getElementById('{{ "delete-form_".$tenant->id }}').submit();">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="32" height="32" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                                 </svg>
                                                            </a>
                                                            <form id="{{ "delete-form_".$tenant->id }}"
                                                                action="{{ route('admin.tenants.destroy', $tenant->id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6">
                                                            <div class="empty-img">
                                                                <img src="{{ asset('static/illustrations/undraw_printing_invoices_5r4r.svg') }}" class="w-100" height="128" alt="">
                                                            </div>
                                                            <p class="empty-title text-danger text-center">Aucun résultat trouvé</p>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer d-flex align-items-center">
                                        <ul class="pagination m-0 ms-auto"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        @include('central.partials.common.blank', [
            'routeName' => 'admin.tenants.create',
            'title' => 'Créer votre premier tenant',
        ])
    @endif
@stop
@section('footer')
    @include('central.partials.footer.bottom')
@stop
@section('ideoreport_libs')
    <script src="{{ asset('dist/libs/tom-select/dist/js/tom-select.base.min.js') }}" defer></script>
    <script src="{{ asset('dist/libs/list/dist/list.min.js') }}" defer></script>
@stop
@section('ideoreport_js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            sortClass: 'table-sort',
            listClass: 'table-tbody',
            valueNames: [
                'sort-doceboidorg',
                'sort-zendeskidorg',
                'sort-name',
                'sort-subdomain',
                {
                    attr: 'data-created',
                    name: 'sort-created'
                },
                {
                    attr: 'data-updated',
                    name: 'sort-updated'
                }
            ],
            page: 10,
            pagination: [{
                name: "pagination",
                paginationClass: "pagination",
                left: 2,
                right: 2,
                item: '<li class="page-item"><a class="btn btn-icon btn-red mx-1 page" href="#"></a></li>'
            }]
        };
        var listjs = new List('table-tenants', options);
        var listjsItemsPerPage = document.getElementById('listjs-items-per-page');
        if(listjsItemsPerPage != null){
            listjsItemsPerPage.addEventListener('change', function(e) {
                var items = this.value;
                listjs.page = items;
                listjs.update();
            });
        }
    })
</script>
@stop
