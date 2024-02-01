@extends('master')
@php
    $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
    $cmi_time_conf = config('tenantconfigfields.enrollmentfields.cmi_time');
    $calculated_time_conf = config('tenantconfigfields.enrollmentfields.calculated_time');
    $recommended_time_conf = config('tenantconfigfields.enrollmentfields.recommended_time');
    $categorie = config('tenantconfigfields.userfields.categorie');
    if($contract_start_date_conf != null)
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d', $contract_start_date_conf);
        $yearOfDate = $date->year;
        $currentYear = now()->year;
        if ($yearOfDate > $currentYear) {
            $statDate = $date->format('d-m-') . now()->year;
        } else {
            $statDate =  $date->format('d-m-') . (now()->year - 1) ;
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
            <div class="row row-deck row-cards">

                @if($contract_start_date_conf !== null)
                    @include('tenant.widgets.stats_inscrits_date', ['inscritsReportFromStatDate' => $inscritsReportFromStatDate])
                @endif

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="ribbon ribbon-start bg-yellow h2">
                                Inscriptions
                            </div>
                            <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block d-none">
                                <div class="row g-2">
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="form-control-wrap">
                                                <div class="input-daterange date-picker-range input-group">
                                                    <input type="date" class="form-control" id="insStartDate">
                                                    <input type="date" class="form-control" id="insEndDate">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0)" class="btn btn-icon" aria-label="Button" id="btnInsFilter" data-bs-toggle="tooltip" data-bs-placement="top" title="Appliquer le filtre">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter-check" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M11.18 20.274l-2.18 .726v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v3"></path>
                                                <path d="M15 19l2 2l4 -4"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0)" class="btn btn-icon" aria-label="Button" id="btnInsReload" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer le filtre">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="col-auto dropdown">
                                        <a href="javascript:void(0)" class="btn dropdown-toggle text-black" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="true" title="Générer votre rapport">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-spreadsheet" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                                    <path d="M8 11h8v7h-8z"></path>
                                                    <path d="M8 15h8"></path>
                                                    <path d="M11 11v7"></path>
                                            </svg>
                                            Générer vos rapports
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end" data-popper-placement="bottom-end">
                                            <a class="dropdown-item" href="">
                                                rapport des inscrits actives
                                            </a>
                                            <a class="dropdown-item" href="">
                                                rapport des inscrits inactives
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="container container-slim py-4 d-none" id="loaderInscrits">
                                <div class="text-center">
                                    <div class="mb-3">
                                        <a href="." class="navbar-brand navbar-brand-autodark"><img src="{{ global_asset('static/logo/logo.svg') }}" height="36" alt=""></a>
                                    </div>
                                    <div class="text-muted mb-3">la préparation de vos données</div>
                                    <div class="progress progress-sm mb-3">
                                        <div class="progress-bar progress-bar-indeterminate"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-cards" id="contentInscrits">
                                <div class="col-md-3">
                                    @include('tenant.widgets.inscrits.card-inscrits-detail' , ['libelle' => "Nombre d'inscrits"  , 'data' =>  $totalInscrits ])
                                </div>
                                <div class="col-md-3">
                                    @include('tenant.widgets.inscrits.card-inscrits-detail' , ['libelle' => "Inscrits actifs"  , 'data' =>  $totalActives  ])
                                </div>
                                <div class="col-md-3">
                                    @include('tenant.widgets.inscrits.card-inscrits-detail' , ['libelle' => "Inscrits inactifs"  , 'data' =>  $totalInactives  ])
                                </div>
                                <div class="col-md-3">
                                    @include('tenant.widgets.inscrits.card-inscrits-detail' , ['libelle' => "Inscrits archivés"  , 'data' =>  $totalArchives  ])
                                </div>
                                @if($categorie)
                                    <div class="col-md-6">
                                        <div class="row row-cards">
                                            <div class="col-lg-12">
                                                <div class="row row-cards">
                                                    <div class="col-md-6">
                                                        @include('tenant.widgets.times.session-time')
                                                        {{--
                                                            <div class="card h-100 bg-danger-lt">
                                                            <div class="card-body">
                                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-session-time">
                                                                    <div class="ribbon ribbon-top bg-red">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                            <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                                        </svg>
                                                                    </div>
                                                                </a>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="subheader">Temps de session</div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline">
                                                                    <div class="h2 mb-0 me-2" id="session">**h **min **s.</div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline">
                                                                    <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                            <path d="M3 21l18 -18"></path>
                                                                            <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                                                        </svg>
                                                                        <span id="avgsession">**h **min **s.</span>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>--}}
                                                    </div>
                                                    <div class="col-md-6 {{ $cmi_time_conf == false ? 'opacity-10' : '' }}">
                                                        <div class="card h-100 bg-bitbucket-lt">
                                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-engaged-time">
                                                                <div class="ribbon ribbon-top bg-bitbucket">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                                    </svg>
                                                                </div>
                                                            </a>
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="subheader">Temps d'engagement</div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline">
                                                                    <div class="h2 mb-0 me-2" id="cmi">**h **min **s.</div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline">
                                                                    <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                            <path d="M3 21l18 -18"></path>
                                                                            <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                                                        </svg>
                                                                        <span id="avgcmi">**h **min **s.</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{ $calculated_time_conf == false ? 'opacity-10' : '' }}">
                                                        <div class="card h-100 bg-yellow-lt">
                                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-calculated-time">
                                                                <div class="ribbon ribbon-top bg-yellow">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                                    </svg>
                                                                </div>
                                                            </a>
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="subheader">Temps calculé</div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline">
                                                                    <div class="h2 mb-0 me-2" id="tc">**h **min **s.</div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline">
                                                                    <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                            <path d="M3 21l18 -18"></path>
                                                                            <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                                                        </svg>
                                                                        <span id="avgtc">**h **min **s.</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 {{ $recommended_time_conf == false ? 'opacity-10' : '' }}">
                                                        <div class="card h-100 bg-green-lt">
                                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-recommended-time">
                                                                <div class="ribbon ribbon-top bg-green">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                                    </svg>
                                                                </div>
                                                            </a>
                                                            <div class="card-body">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="subheader">Temps pédagogique recommandé</div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline">
                                                                    <div class="h2 mb-0 me-2" id="tpr">**h **min **s.</div>
                                                                </div>
                                                                <div class="d-flex align-items-baseline">
                                                                    <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                            <path d="M3 21l18 -18"></path>
                                                                            <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                                                        </svg>
                                                                        <span id="avgtpr">**h **min **s.</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-12">
                                                @include('tenant.widgets.inscrits.chart-inscrits-categorie')
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        @include('tenant.widgets.inscrits.chart-inscrits-categorie-status')
                                    </div>
                                @else
                                    <div class="col-md-3">
                                        <div class="card h-100 bg-danger-lt">
                                            <div class="card-body">
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-session-time">
                                                    <div class="ribbon ribbon-top bg-red">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                        </svg>
                                                    </div>
                                                </a>
                                                <div class="d-flex align-items-center">
                                                    <div class="subheader">Temps de session</div>
                                                </div>
                                                <div class="d-flex align-items-baseline">
                                                    <div class="h2 mb-0 me-2" id="session">**h **min **s.</div>
                                                </div>
                                                <div class="d-flex align-items-baseline">
                                                    <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M3 21l18 -18"></path>
                                                            <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                                        </svg>
                                                        <span id="avgsession">**h **min **s.</span>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 {{ $cmi_time_conf == false ? 'opacity-10' : '' }}">
                                        <div class="card h-100 bg-bitbucket-lt">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-engaged-time">
                                                <div class="ribbon ribbon-top bg-bitbucket">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="subheader">Temps d'engagement</div>
                                                </div>
                                                <div class="d-flex align-items-baseline">
                                                    <div class="h2 mb-0 me-2" id="cmi">**h **min **s.</div>
                                                </div>
                                                <div class="d-flex align-items-baseline">
                                                    <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M3 21l18 -18"></path>
                                                            <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                                        </svg>
                                                        <span id="avgcmi">**h **min **s.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 {{ $calculated_time_conf == false ? 'opacity-10' : '' }}">
                                        <div class="card h-100 bg-yellow-lt">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-calculated-time">
                                                <div class="ribbon ribbon-top bg-yellow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="subheader">Temps calculé</div>
                                                </div>
                                                <div class="d-flex align-items-baseline">
                                                    <div class="h2 mb-0 me-2" id="tc">**h **min **s.</div>
                                                </div>
                                                <div class="d-flex align-items-baseline">
                                                    <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M3 21l18 -18"></path>
                                                            <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                                        </svg>
                                                        <span id="avgtc">**h **min **s.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 {{ $recommended_time_conf == false ? 'opacity-10' : '' }}">
                                        <div class="card h-100 bg-green-lt">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-recommended-time">
                                                <div class="ribbon ribbon-top bg-green">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="subheader">Temps pédagogique recommandé</div>
                                                </div>
                                                <div class="d-flex align-items-baseline">
                                                    <div class="h2 mb-0 me-2" id="tpr">**h **min **s.</div>
                                                </div>
                                                <div class="d-flex align-items-baseline">
                                                    <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M3 21l18 -18"></path>
                                                            <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                                        </svg>
                                                        <span id="avgtpr">**h **min **s.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer')
    @include('tenant.partials.footer.bottom')
@stop
@section('ideoreport_libs')
    @if($categorie)
        <script src="{{ $chartsInscrits['chartInscritPerCategorie']->cdn() }}"></script>
        <script src="{{ $chartsInscrits['chartInscritPerCategorieAndStatus']->cdn() }}"></script>
    @endif
@stop
@section('ideoreport_js')
    @if($categorie)
        {{ $chartsInscrits['chartInscritPerCategorie']->script() }}
        {{ $chartsInscrits['chartInscritPerCategorieAndStatus']->script() }}
    @endif
@stop
