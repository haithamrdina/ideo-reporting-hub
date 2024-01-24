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
                        Gérer votre Tenant
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        @include('central.tenants.includes.small-client-menu')
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    @include('central.partials.common.alert')
                </div>
            </div>
        </div>
    </div>
@stop
@section('page-content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="row g-0">
                    <div class="col-2 d-none d-md-block border-end">
                        @include('central.tenants.includes.client-menu')
                    </div>
                    <div class="col d-flex flex-column">
                        <div>
                            <div class="row align-items-center p-2 m-2">
                                <div class="col-auto">
                                    <h2>Liste des groupes</h2>
                                </div>
                            </div>
                            <div>
                                <div id="table-groupes" class="table-responsive">
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
                                                    <th><button class="table-sort" data-sort="sort-doceboid">ID docebo</button></th>
                                                    <th><button class="table-sort" data-sort="sort-code">Code du groupe</button></th>
                                                    <th><button class="table-sort" data-sort="sort-name">Nom du groupe</button></th>
                                                    <th><button class="table-sort" data-sort="sort-status">Statut</button></th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-tbody">
                                                @php
                                                    use Carbon\Carbon;
                                                @endphp
                                                @if (count($groupes) > 0)
                                                    @foreach ($groupes as $groupe)
                                                            <td class="sort-docebo_id">
                                                                {{ $groupe->docebo_id}}
                                                            </td>
                                                            <td class="sort-code">
                                                                {{ $groupe->code}}
                                                            </td>
                                                            <td class="sort-name">
                                                                {{ $groupe->name}}
                                                            </td>
                                                            <td class="sort-status">
                                                                @if($groupe->status->value == 1)
                                                                    <span class="badge badge-outline text-green">active</span>
                                                                @else
                                                                    <span class="badge badge-outline text-red">inactive</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                <a href="{{ route('admin.tenants.groups.edit', ['tenant' => $tenant->id, 'group' => $groupe->id]) }}" class="btn btn-primary btn-icon"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="{{ $groupe->status->value == 1 ? 'désactiver ce groupe' : 'activer ce groupe' }}">
                                                                    @if($groupe->status->value == 1)
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-binary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                            <path d="M11 10v-5h-1m8 14v-5h-1" />
                                                                            <path d="M15 5m0 .5a.5 .5 0 0 1 .5 -.5h2a.5 .5 0 0 1 .5 .5v4a.5 .5 0 0 1 -.5 .5h-2a.5 .5 0 0 1 -.5 -.5z" />
                                                                            <path d="M10 14m0 .5a.5 .5 0 0 1 .5 -.5h2a.5 .5 0 0 1 .5 .5v4a.5 .5 0 0 1 -.5 .5h-2a.5 .5 0 0 1 -.5 -.5z" />
                                                                            <path d="M6 10h.01m-.01 9h.01" />
                                                                        </svg>
                                                                    @else
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-binary-off" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                            <path d="M11 7v-2h-1" />
                                                                            <path d="M18 19v-1" />
                                                                            <path d="M15.5 5h2a.5 .5 0 0 1 .5 .5v4a.5 .5 0 0 1 -.5 .5h-2a.5 .5 0 0 1 -.5 -.5v-4a.5 .5 0 0 1 .5 -.5z" />
                                                                            <path d="M10.5 14h2a.5 .5 0 0 1 .5 .5v4a.5 .5 0 0 1 -.5 .5h-2a.5 .5 0 0 1 -.5 -.5v-4a.5 .5 0 0 1 .5 -.5z" />
                                                                            <path d="M6 10v.01" />
                                                                            <path d="M6 19v.01" />
                                                                            <path d="M3 3l18 18" />
                                                                        </svg>
                                                                    @endif
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="7">
                                                            <div class="empty-img">
                                                                <img src="{{ global_asset('static/illustrations/no-data-found.svg') }}"
                                                                    class="w-100" height="128" alt="">
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
@stop
@section('footer')
    @include('central.partials.footer.bottom')
@stop
@section('ideoreport_libs')
    <script src="{{ asset('dist/libs/list/dist/list.min.js') }}" defer></script>
@stop
@section('ideoreport_js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            sortClass: 'table-sort',
            listClass: 'table-tbody',
            valueNames: [
                'sort-doceboid',
                'sort-code',
                'sort-name',
                'sort-status',
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
        var listjs = new List('table-groupes', options);
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
