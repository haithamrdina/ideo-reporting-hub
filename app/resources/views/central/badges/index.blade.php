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
                        Badges
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
                                    <h2>Liste des badges</h2>
                                </div>
                            </div>
                            <div>
                                <div id="table-lps" class="table-responsive">
                                    <div class="card-body border-bottom py-3">
                                        <div class="d-flex">
                                            <div class="text-muted">
                                                Show
                                                <div class="mx-2 d-inline-block">
                                                    <div class="mx-2 d-inline-block">
                                                        <select class="form-select h-5" id="listjs-items-per-page">
                                                            <option value="17" selected>17</option>
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
                                                    <th><button class="table-sort" data-sort="sort-code">Code</button></th>
                                                    <th><button class="table-sort" data-sort="sort-name">Name</button></th>
                                                    <th><button class="table-sort" data-sort="sort-points">Points</button></th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-tbody">
                                                @php
                                                    use Carbon\Carbon;
                                                @endphp
                                                @if (count($badges) > 0)
                                                    @foreach ($badges as $badge)
                                                            <td class="sort-doceboid">
                                                                {{ $badge->docebo_id}}
                                                            </td>
                                                            <td class="sort-code">
                                                                {{ $badge->code}}
                                                            </td>
                                                            <td class="sort-name">
                                                                {{ $badge->name}}
                                                            </td>
                                                            <td class="sort-points">
                                                                {{ $badge->points}}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4">
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
                'sort-points',
            ],
            page: 17,
            pagination: [{
                name: "pagination",
                paginationClass: "pagination",
                left: 2,
                right: 2,
                item: '<li class="page-item"><a class="btn btn-icon btn-red mx-1 page" href="#"></a></li>'
            }]
        };
        var listjs = new List('table-lps', options);
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
