@extends('master')
@php
    $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
    $archive = config('tenantconfigfields.archive');
    $sur_mesure = config('tenantconfigfields.sur_mesure');
    $cmi_time_conf = config('tenantconfigfields.enrollmentfields.cmi_time');
    $calculated_time_conf = config('tenantconfigfields.enrollmentfields.calculated_time');
    $recommended_time_conf = config('tenantconfigfields.enrollmentfields.recommended_time');
    $categorie = config('tenantconfigfields.userfields.categorie');
    if ($contract_start_date_conf != null) {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
        $yearOfDate = $date->year;
        $currentYear = now()->year;
        if ($yearOfDate > $currentYear) {
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
                    <div class="page-pretitle">
                        IDEO Reporting
                    </div>
                    <h2 class="page-title">
                        Vos données de progression sur la e-académie.
                    </h2>
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
                        <div class="text-muted mb-3">la préparation de vos données</div>
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
    <script src="{{ global_asset('scripts/plateforme/plateforme.js') }}"></script>
@stop
