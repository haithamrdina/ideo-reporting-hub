@extends('master')
@php
    $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
    $archive = config('tenantconfigfields.archive');
    $sur_mesure = config('tenantconfigfields.sur_mesure');
    $gamification = config('tenantconfigfields.gamification');
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
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-red d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#modal-report-plateforme">
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
                            data-bs-target="#modal-report-plateforme" aria-label="Générer vos rapports">
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
            </div>
        </div>
    </div>
@stop
@section('page-content')
    @if ($gamification !== false)
        <div class="page-body">
            <div class="container-fluid">
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
                <div class="row row-deck row-cards d-none" id="content">
                    <div class="col-lg-9 col-md-12">
                        <div class="row row-deck row-cards">
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
                    <div class="col-lg-3 col-md-12">
                        <div class="row row-cards">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="ribbon ribbon-start bg-green h2">
                                            Gamification
                                        </div>
                                        <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block">
                                            <div class="row g-2">
                                                <div class="col-auto">
                                                    <a href="{{ route('tenant.plateforme.gamification.export') }}"
                                                        class="btn btn-icon text-black" aria-label="Button"
                                                        id="btnGamificationExport" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Générer votre rapport">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-file-spreadsheet"
                                                            width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                                            <path
                                                                d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                                            </path>
                                                            <path d="M8 11h8v7h-8z"></path>
                                                            <path d="M8 15h8"></path>
                                                            <path d="M11 11v7"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div>
                                                <div class="card-header bg-green-lt">
                                                    <h3 class="card-title">Leaderboard : Top 10</h3>
                                                </div>
                                                <table class="table card-table table-vcenter">
                                                    <thead>
                                                        <tr>
                                                            <th>Apprenant</th>
                                                            <th colspan="2">Score</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @isset($leaderboard)
                                                            @foreach ($leaderboard as $data)
                                                                <tr>
                                                                    <td>
                                                                        <div class="row align-items-center">
                                                                            <div class="col-auto">
                                                                                <div class="font-weight-medium">
                                                                                    {{ $data['fullname'] }}</br>{{ $data['username'] }}
                                                                                </div>
                                                                                <div class="text-warning">
                                                                                    {{ $data['group'] }}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>{{ $data['points'] }}</td>
                                                                    <td class="w-33">
                                                                        <div class="progress progress-xs">
                                                                            <div class="progress-bar bg-primary"
                                                                                style="{{ 'width: ' . $data['percentage'] . '%' }}">
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endisset
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12">
                                            <div>
                                                <div class="card-header bg-blue-lt">
                                                    <h3 class="card-title">Liste des badges</h3>
                                                </div>
                                                <table class="table card-table table-vcenter">
                                                    <thead>
                                                        <tr>
                                                            <th>Badge</th>
                                                            <th colspan="2">Détail</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @isset($badgeData)
                                                            @foreach ($badgeData as $item)
                                                                <tr>
                                                                    <td>
                                                                        <div class="row align-items-center">
                                                                            <div class="col-auto">
                                                                                <div class="font-weight-medium">
                                                                                    {{ $item['name'] }}
                                                                                </div>
                                                                                <div class="text-warning">
                                                                                    {{ $item['code'] }}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td colspan="2" class="w-50">
                                                                        <div class="row align-items-center">
                                                                            <div class="col-auto">
                                                                                <div class="font-weight-medium">
                                                                                    points : {{ $item['points'] }}.
                                                                                </div>
                                                                                <div class="text-warning">
                                                                                    total apprenants : {{ $item['total'] }}.
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endisset
                                                    </tbody>
                                                </table>
                                            </div>
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
        <div class="page-body">
            <div class="container-xl">
                <!-- loader -->
                <div class="page page-center" id="loader">
                    <div class="container container-slim py-4">
                        <div class="text-center mt-8">
                            <div class="mb-3">
                                <a href="." class="navbar-brand navbar-brand-autodark"><img
                                        src="{{ global_asset('static/logo/logo.svg') }}" height="36"
                                        alt=""></a>
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
    @endif
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
