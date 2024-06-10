@extends('master')
@php
    $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
    $cmi_time_conf = config('tenantconfigfields.enrollmentfields.cmi_time');
    $calculated_time_conf = config('tenantconfigfields.enrollmentfields.calculated_time');
    $recommended_time_conf = config('tenantconfigfields.enrollmentfields.recommended_time');
    $categorie = config('tenantconfigfields.userfields.categorie');
    if ($contract_start_date_conf != null) {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
        $yearOfDate = $date->year;
        $currentYear = now()->year;
        if ($yearOfDate < $currentYear) {
            $statDate = $date->format('d-m-') . now()->year;
        } else {
            $statDate = $date->format('d-m-') . (now()->year - 1);
        }
    }
@endphp
@section('title')
    Tableau de bord
@stop
@section('ideoreport_css')
@stop
@section('header')
    @include('tenant.partials.navbar.overlap-topbar')
@stop
@section('page-header')
    <div class="page-header d-print-none text-white">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <span class="h1 text-red">
                        @if (isset($project))
                            {{ $project->name }}
                        @elseif(isset($group))
                            {{ $group->name }}
                        @endif
                    </span>
                    <!-- Page pre-title -->
                    <div class="page-pretitle mt-2">
                        Ideo Reporting
                    </div>
                    <h2 class="page-title">
                        Vos données de progression sur la e-académie.
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-red d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#modal-report-project">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-spreadsheet"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                </path>
                                <path d="M8 11h8v7h-8z"></path>
                                <path d="M8 15h8"></path>
                                <path d="M11 11v7"></path>
                            </svg>
                            Générer vos rapports
                        </a>
                        <a href="#" class="btn btn-red d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#modal-report-project" aria-label="Générer vos rapports">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-spreadsheet"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                </path>
                                <path d="M8 11h8v7h-8z"></path>
                                <path d="M8 15h8"></path>
                                <path d="M11 11v7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none d-none">
                    <div class="btn-list">
                        <div class="mb-3 text-black">
                            <div class="input-group mb-2">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-affiliate"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#C2181A"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5.931 6.936l1.275 4.249m5.607 5.609l4.251 1.275"></path>
                                        <path d="M11.683 12.317l5.759 -5.759"></path>
                                        <path d="M5.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"></path>
                                        <path d="M18.5 5.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"></path>
                                        <path d="M18.5 18.5m-1.5 0a1.5 1.5 0 1 0 3 0a1.5 1.5 0 1 0 -3 0"></path>
                                        <path d="M8.5 15.5m-4.5 0a4.5 4.5 0 1 0 9 0a4.5 4.5 0 1 0 -9 0"></path>
                                    </svg>
                                </span>
                                <select type="text" class="form-select" id="select-projects" value="">
                                    <option value="{{ $project->id }}"> {{ $project->name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('page-content')
    <div class="page-body">
        <div class="container-xl">
            <!-- loader -->
            <div class="page page-center" id="loader">
                <div class="container container-slim py-4">
                    <div class="text-center mt-8">
                        <div class="mb-3">
                            <a href="." class="navbar-brand navbar-brand-autodark"><img
                                    src="{{ global_asset('static/logo/logo.svg') }}" height="36" alt=""></a>
                        </div>
                        <div class="text-muted mb-3">Vos données sont en cours de préparation, merci de patienter un
                            instant.</div>
                        <div class="progress progress-sm mb-3">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- loader -->
            <div class="row row-deck row-cards d-none" id="content">
                @if ($contract_start_date_conf !== null)
                    @include('tenant.widgets.inscrit-per-date')
                @endif
                @include('tenant.widgets.inscrit')
                @include('tenant.widgets.module')
                @include('tenant.widgets.lp')
                @include('tenant.widgets.lsc')
            </div>
            @include('tenant.widgets.modal')
        </div>
    </div>
@stop
@section('footer')
    @include('tenant.partials.footer.bottom')
@stop
@section('ideoreport_libs')
    <script src="{{ global_asset('dist/libs/tom-select/dist/js/tom-select.base.min.js') }}" defer></script>
    <script src="{{ global_asset('dist/libs/apexcharts/dist/apexcharts.min.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop
@section('ideoreport_js')
    <script src="{{ global_asset('scripts/project/project.js') }}"></script>
@stop
