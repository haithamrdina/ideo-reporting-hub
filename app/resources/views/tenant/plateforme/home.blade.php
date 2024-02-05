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
                    <div class="col-12 ">
                        <div class="row row-cards">
                            <div class="col-md-12 col-lg-12">
                                <div class="card card-active">
                                    <div class="ribbon ribbon-start bg-red h2">
                                        Statistiques des inscriptions effectuées à partir du {{ $statDate }} .
                                    </div>
                                    <div class="card-body mt-6">
                                        <div class="row row-cards">
                                            <div class="col-md-4">
                                                @include('tenant.widgets.inscrits.card-inscrits-detail' , ['card_title' => 'Nombre des nouveaux inscrits' , 'card_data' => $learnersInscriptionsPerStatDate['total'] ])
                                            </div>
                                            <div class="col-md-4">
                                                @include('tenant.widgets.inscrits.card-inscrits-detail' , ['card_title' => 'Nombre d\'inscrits actifs', 'card_data' => $learnersInscriptionsPerStatDate['active']])
                                            </div>
                                            <div class="col-md-4">
                                                @include('tenant.widgets.inscrits.card-inscrits-detail' , ['card_title' => 'Nombre d\'inscrits inactives' , 'card_data' => $learnersInscriptionsPerStatDate['inactive']])
                                            </div>
                                            <div class="col-md-3">
                                                @include('tenant.widgets.times.session-time' , ['total_session_time' => $timingDetailsPerStatDate['total_session_time'], 'avg_session_time' => $timingDetailsPerStatDate['avg_session_time']])
                                            </div>
                                            <div class="col-md-3 {{ $cmi_time_conf == false ? 'opacity-10' : ''  }}">
                                                @include('tenant.widgets.times.cmi-time' , ['total_cmi_time' => $timingDetailsPerStatDate['total_cmi_time'], 'avg_cmi_time' => $timingDetailsPerStatDate['avg_cmi_time']])
                                            </div>
                                            <div class="col-md-3 {{ $calculated_time_conf == false ? 'opacity-10' : '' }}">
                                                @include('tenant.widgets.times.calculated-time' , ['total_calculated_time' => $timingDetailsPerStatDate['total_calculated_time'], 'avg_calculated_time' => $timingDetailsPerStatDate['avg_calculated_time']])
                                            </div>
                                            <div class="col-md-3 {{ $recommended_time_conf == false ? 'opacity-10' : '' }}">
                                                @include('tenant.widgets.times.recommended-time' , ['total_recommended_time' => $timingDetailsPerStatDate['total_recommended_time'], 'avg_recommended_time' => $timingDetailsPerStatDate['avg_recommended_time']])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                            <div class="row row-cards">
                                <div class="col-md-3">
                                    @include('tenant.widgets.inscrits.card-inscrits-detail' , ['card_title' => "Nombre d'inscrits"  , 'card_data' =>  $learnersInscriptions['total']])
                                </div>
                                <div class="col-md-3">
                                    @include('tenant.widgets.inscrits.card-inscrits-detail' , ['card_title' => "Inscrits actifs"  , 'card_data' =>  $learnersInscriptions['active']])
                                </div>
                                <div class="col-md-3">
                                    @include('tenant.widgets.inscrits.card-inscrits-detail' , ['card_title' => "Inscrits inactifs"  , 'card_data' =>  $learnersInscriptions['inactive']])
                                </div>
                                <div class="col-md-3">
                                    @include('tenant.widgets.inscrits.card-inscrits-detail' , ['card_title' => "Inscrits archivés"  , 'card_data' =>  $learnersInscriptions['archive']])
                                </div>
                                <div class="col-md-3">
                                    @include('tenant.widgets.times.session-time' , ['total_session_time' => $timingDetails['total_session_time'], 'avg_session_time' => $timingDetails['avg_session_time']])
                                </div>
                                <div class="col-md-3 {{ $cmi_time_conf == false ? 'opacity-10' : ''  }}">
                                    @include('tenant.widgets.times.cmi-time' , ['total_cmi_time' => $timingDetails['total_cmi_time'], 'avg_cmi_time' => $timingDetails['avg_cmi_time']])
                                </div>
                                <div class="col-md-3 {{ $calculated_time_conf == false ? 'opacity-10' : '' }}">
                                    @include('tenant.widgets.times.calculated-time' , ['total_calculated_time' => $timingDetails['total_calculated_time'], 'avg_calculated_time' => $timingDetails['avg_calculated_time']])
                                </div>
                                <div class="col-md-3 {{ $recommended_time_conf == false ? 'opacity-10' : '' }}">
                                    @include('tenant.widgets.times.recommended-time' , ['total_recommended_time' => $timingDetails['total_recommended_time'], 'avg_recommended_time' => $timingDetails['avg_recommended_time']])
                                </div>
                                @if($categorie)
                                    <div class="col-md-4">
                                        @include('tenant.widgets.inscrits.chart-inscrits-categorie')
                                    </div>
                                    <div class="col-md-8">
                                        @include('tenant.widgets.inscrits.chart-inscrits-categorie-status')
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="ribbon ribbon-start bg-bleu h2">
                                Modules
                            </div>
                            <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block">
                                <div class="row g-2">
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
                                                rapport de formation softskills
                                            </a>
                                            <a class="dropdown-item" href="">
                                                rapport de formation digital
                                            </a>
                                            <a class="dropdown-item" href="">
                                                rapport de formation langue
                                            </a>
                                            <a class="dropdown-item" href="">
                                                rapport des inscriptions moocs
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="row row-cards">
                                <div class="col-lg-6">
                                    @include('tenant.widgets.modules.soft')
                                </div>
                                <div class="col-lg-6">
                                    @include('tenant.widgets.modules.digital')
                                </div>
                                <div class="col-lg-4">
                                  @include('tenant.widgets.modules.speex-global')
                                </div>
                                <div class="col-lg-8">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h3 class="card-title">Statistique du formation langue par niveau et temps de formation</h3>
                                            <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block d-none">
                                                <div class="row g-2">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <select type="text" class="form-select" id="select-lgs" value=""></select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row" id="contentLG">
                                                <div class="col-lg-12">
                                                    <div id="chart-speex"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    @include('tenant.widgets.modules.mooc')
                                </div>
                                <div class="col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <h3 class="card-title">Répartition de temps de session,temps d'engagement, temps calculé et le temps pédagogique recommandé par catégorie :</h3>
                                            </div>
                                            <div id="chart-combination">
                                                {{ $timingChart  !=null  ? $timingChart->render() : ''}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    @include('tenant.widgets.lp')
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="ribbon ribbon-start bg-cyan h2">
                                Learner success center
                            </div>
                            <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block d-none">
                                <div class="row g-2">
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="form-control-wrap">
                                                <div class="input-daterange date-picker-range input-group">
                                                    <input type="date" class="form-control" id="lscStartDate">
                                                    <input type="date" class="form-control" id="lscEndDate">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0)" class="btn btn-icon" aria-label="Button" id="btnLscFilter" data-bs-toggle="tooltip" data-bs-placement="top" title="Appliquer le filtre">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-filter-check" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M11.18 20.274l-2.18 .726v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v3"></path>
                                                <path d="M15 19l2 2l4 -4"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0)" class="btn btn-icon" aria-label="Button" id="btnLscReload" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer le filtre">
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
                                                rapport des tickets
                                            </a>
                                            <a class="dropdown-item" href="">
                                                rapport des appels
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="container container-slim py-4 d-none" id="loaderLsc">
                                <div class="text-center">
                                    <div class="mb-3">
                                        <a href="." class="navbar-brand navbar-brand-autodark"><img src="{{ asset('static/logo/logo.svg') }}" height="36" alt=""></a>
                                    </div>
                                    <div class="text-muted mb-3">la préparation de vos données</div>
                                    <div class="progress progress-sm mb-3">
                                        <div class="progress-bar progress-bar-indeterminate"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-cards" id="contentLsc">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center">
                                                <div class="h2 mb-0 me-2">Total des tickets
                                                    <span class="h1 mb-0 me-2" id="tickets">&nbsp;{{ $lscStats['totalTickets'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-center">
                                                <div class="h2 mb-0 me-2">Total des appels
                                                    <span class="h1 mb-0 me-2" id="calls">&nbsp;{{ $lscStats['totalCalls'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-5">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <h3 class="card-title">Répartition des tickets par status :</h3>
                                            </div>
                                            <div class="col-auto">
                                                <div id="chart-ticket-pie">
                                                    {{  $lscStats['ticketsCharts']  !=null  ?  $lscStats['ticketsCharts']->render() : ''}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-7">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <h3 class="card-title">Répartition des appels par sujet et type :</h3>
                                            </div>
                                            <div class="col-auto">
                                                <div id="chart-calls-sujet-type">
                                                    {{  $lscStats['callsPerSubjectAndTypeChart']  !=null  ?  $lscStats['callsPerSubjectAndTypeChart']->render() : ''}}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <h3 class="card-title">Répartition des appels par statut et type :</h3>
                                            </div>
                                            <div class="col-auto">
                                                <div id="chart-calls-statut-type">
                                                    {{  $lscStats['callsPerStatutAndTypeChart']  !=null  ?  $lscStats['callsPerStatutAndTypeChart']->render() : ''}}

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
        </div>
    </div>
@stop
@section('footer')
    @include('tenant.partials.footer.bottom')
@stop
@section('ideoreport_libs')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ global_asset('dist/libs/tom-select/dist/js/tom-select.base.min.js') }}" defer></script>
@stop
@section('ideoreport_js')
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-enis'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
  <script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
    	var el;
    	window.TomSelect && (new TomSelect(el = document.getElementById('select-lps'), {
    		copyClassesToDropdown: false,
    		dropdownParent: 'body',
    		controlInput: '<input>',
    		render:{
    			item: function(data,escape) {
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    			option: function(data,escape){
    				if( data.customProperties ){
    					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
    				}
    				return '<div>' + escape(data.text) + '</div>';
    			},
    		},
    	}));
    });
    // @formatter:on
  </script>
@stop
