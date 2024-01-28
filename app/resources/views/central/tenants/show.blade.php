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
                        <div class="card-body">
                            <div class="row row-deck row-cards">
                                <div class="col-12">
                                    <div class="row row-cards">
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="card card-sm">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <span class="bg-primary text-white avatar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-binary-tree" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <path d="M6 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"/>
                                                                    <path d="M16 4a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"/>
                                                                    <path d="M16 20a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"/>
                                                                    <path d="M11 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"/>
                                                                    <path d="M21 12a2 2 0 1 0 -4 0a2 2 0 0 0 4 0z"/>
                                                                    <path d="M5.058 18.306l2.88 -4.606"/>
                                                                    <path d="M10.061 10.303l2.877 -4.604"/>
                                                                    <path d="M10.065 13.705l2.876 4.6"/>
                                                                    <path d="M15.063 5.7l2.881 4.61"/>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="col">
                                                            <div class="font-weight-medium">
                                                                {{ $stats->groups }} Groupes
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="card card-sm">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <span class="bg-green text-white avatar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users-group" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                                                    <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1"/>
                                                                    <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                                                    <path d="M17 10h2a2 2 0 0 1 2 2v1"/>
                                                                    <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                                                    <path d="M3 13v-1a2 2 0 0 1 2 -2h2"/>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="col">
                                                            <div class="font-weight-medium">
                                                                {{ $stats->learners }} Apprenants
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="card card-sm">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <span class="bg-facebook text-white avatar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-ticket" width="24" height="24" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                    <path d="M15 5l0 2"></path>
                                                                    <path d="M15 11l0 2"></path>
                                                                    <path d="M15 17l0 2"></path>
                                                                    <path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2"></path>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="col">
                                                            <div class="font-weight-medium">
                                                                {{ $stats->tickets }} tickets
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="card card-sm">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <span class="bg-facebook text-white avatar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-headset" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <path d="M4 14v-3a8 8 0 1 1 16 0v3"/>
                                                                    <path d="M18 19c0 1.657 -2.686 3 -6 3"/>
                                                                    <path d="M4 14a2 2 0 0 1 2 -2h1a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-1a2 2 0 0 1 -2 -2v-3z"/>
                                                                    <path d="M15 14a2 2 0 0 1 2 -2h1a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-1a2 2 0 0 1 -2 -2v-3z"/>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="col">
                                                            <div class="font-weight-medium">
                                                                {{ $stats->calls }} Appels
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- E-leanrning Stats -->
                                        <div class="col-sm-6 col-lg-2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="subheader">Plans de formation</div>
                                                    </div>
                                                    <div class="h1">{{ $stats->lps }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="subheader">SUR MESURE</div>
                                                    </div>
                                                    <div class="h1">{{ $stats->sm }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="subheader">CEGOS</div>
                                                    </div>
                                                    <div class="h1">{{ $stats->cegos }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="subheader">ENI</div>
                                                    </div>
                                                    <div class="h1">{{ $stats->eni }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="subheader">SPEEX</div>
                                                    </div>
                                                    <div class="h1">{{ $stats->speex }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-2">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="subheader">Mooc</div>
                                                    </div>
                                                    <div class="h1">{{ $stats->moocs }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs">
                                                <li class="nav-item">
                                                    <a href="#tabs-update" class="nav-link active" data-bs-toggle="tab">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments-cog" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                            <path d="M6 4v4" /><path d="M6 12v8" />
                                                            <path d="M13.199 14.399a2 2 0 1 0 -1.199 3.601" />
                                                            <path d="M12 4v10" />
                                                            <path d="M12 18v2" />
                                                            <path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                            <path d="M18 4v1" />
                                                            <path d="M18 9v2.5" />
                                                            <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                            <path d="M19.001 15.5v1.5" />
                                                            <path d="M19.001 21v1.5" />
                                                            <path d="M22.032 17.25l-1.299 .75" />
                                                            <path d="M17.27 20l-1.3 .75" />
                                                            <path d="M15.97 17.25l1.3 .75" />
                                                            <path d="M20.733 20l1.3 .75" />
                                                        </svg>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <h3 class="card-title">Liste des mise à jour</h3>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content">
                                                <div class="tab-pane active show" id="tabs-update">
                                                    <div class="row">
                                                        <div class="col-md-6 col-lg-6 mb-3">
                                                            <div class="row">
                                                                <div class="col-md-12 col-lg-12 mt-3 mb-3">
                                                                    <div class="card">
                                                                        <div class="card-header">
                                                                            <h3 class="card-title">Mise à jour des groupes et des apprenants</h3>
                                                                        </div>
                                                                        <div class="card-table table-responsive">
                                                                            <table class="table table-vcenter">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Libellé</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des groupes
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('admin.tenants.groups.maj' , ['tenant' => $tenant->id]) }}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des groupes">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des apprenants
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('admin.tenants.learners.maj' , ['tenant' => $tenant->id]) }}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des apprenants">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 col-lg-12 mb-3">
                                                                    <div class="card">
                                                                        <div class="card-header">
                                                                            <h3 class="card-title">Mise à jour des inscriptions</h3>
                                                                        </div>
                                                                        <div class="card-table table-responsive">
                                                                            <table class="table table-vcenter">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Libellé</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des inscriptions aux modules softskills, digitals et sur mesure.
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('admin.tenants.modules.enroll.maj', ['tenant' => $tenant->id]) }}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des inscriptions aux modules softskills">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des inscriptions aux modules de langue
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('admin.tenants.langues.enroll.maj', ['tenant' => $tenant->id]) }}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des inscriptions aux modules digital">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des inscriptions aux moocs
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('admin.tenants.moocs.enroll.maj', ['tenant' => $tenant->id]) }}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des inscriptions aux moocs">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des inscriptions aux plans de formation
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('admin.tenants.lps.enroll.maj', ['tenant' => $tenant->id]) }}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des inscriptions plans de formation">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-6 mt-3 mb-3">
                                                            <div class="row">
                                                                <div class="col-md-12 col-lg-12 mb-3">
                                                                    <div class="card">
                                                                        <div class="card-header">
                                                                            <h3 class="card-title">Mise à jour des tickets et des appels</h3>
                                                                        </div>
                                                                        <div class="card-table table-responsive">
                                                                            <table class="table table-vcenter">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Libellé</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des Tickets
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('admin.tenants.tickets.maj', ['tenant' => $tenant->id]) }}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des tickets">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des appels téléphoniques
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('admin.tenants.calls.maj', ['tenant' => $tenant->id]) }}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des appels téléphoniques">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 col-lg-12 mb-3">
                                                                    <div class="card">
                                                                        <div class="card-header">
                                                                            <h3 class="card-title">Mise à jour des plans de formation, des modules et des moocs</h3>
                                                                        </div>
                                                                        <div class="card-table table-responsive">
                                                                            <table class="table table-vcenter">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Libellé</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des plans de formation
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{route('admin.tenants.lps.maj' , ['tenant' => $tenant->id])}}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des plans de formation">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des modules
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{route('admin.tenants.modules.maj' , ['tenant' => $tenant->id])}}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des modules">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        Mise à jour des mooc
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('admin.tenants.moocs.maj', ['tenant' => $tenant->id]) }}" class="ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Lancer la mise à jour des moocs">
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                                                <path d="M9 15l6 -6"/>
                                                                                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"/>
                                                                                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"/>
                                                                                            </svg>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
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
@stop
@section('ideoreport_js')

@stop
