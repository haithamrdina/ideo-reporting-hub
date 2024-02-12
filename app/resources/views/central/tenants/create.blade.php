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
                        Créer un nouveau tenant
                    </h2>
                </div>
            </div>
        </div>
    </div>
@stop
@section('page-content')
    <div class="page-body">
        <div class="container-xl">
            @include('central.partials.common.alert')
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Les informations du tenant</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.tenants.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Code') }}</label>
                                            <input id="company_code" type="text"
                                                class="form-control @error('company_code') is-invalid @enderror"
                                                name="company_code" value="{{ old('company_code') }}"
                                                placeholder="company code" autocomplete="company_code" autofocus>
                                            @error('company_code')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Nom du tenant') }}</label>
                                            <input id="company_name" type="text"
                                                class="form-control @error('company_name') is-invalid @enderror"
                                                name="company_name" value="{{ old('company_name') }}"
                                                placeholder="company name" autocomplete="company_name" autofocus>
                                            @error('company_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Docebo Organizations') }}</label>
                                            <select type="text" class="form-select"
                                                placeholder="Select docebo organisation" id="docebo_org_id"
                                                name="docebo_org_id" value="">
                                                @foreach ($clients as $client)
                                                    <option value="{{ $client->client_org_id }}">
                                                        {{ $client->client_org_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('docebo_org_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Zendesk Organisations') }}</label>
                                            <select type="text" class="form-select"
                                                placeholder="Select zendesk oragnisation" id="zendesk_org_id"
                                                name="zendesk_org_id" value="">
                                                @foreach ($organizations as $org)
                                                    <option value="{{ $org->zendesk_org_id }}">
                                                        {{ $org->zendesk_org_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('zendesk_org_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Subdomain') }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    @env('production')
                                                    https://
                                                @else
                                                    http://
                                                    @endenv
                                                </span>
                                                <input id="subdomain"
                                                    type="text"class="form-control @error('subdomain') is-invalid @enderror"
                                                    name="subdomain" value="{{ old('subdomain') }}" placeholder="subdomain"
                                                    autocomplete="subdomain" autofocus>
                                                <span class="input-group-text">
                                                    {{ config('app.domain') }}
                                                </span>
                                            </div>
                                            @error('subdomain')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="m-5 hr-text hr-text-center hr-text-spaceless">
                                            <label class="form-label">Paramètres des mise à jour</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <div class="form-label">Champs supplémentaires pour les apprenants :</div>
                                                    <div>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" id="matricule" name="matricule">
                                                            <span class="form-check-label">Matricule</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" id="fonction" name="fonction">
                                                            <span class="form-check-label">Fonction</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"  id="direction" name="direction">
                                                            <span class="form-check-label">Direction</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"  id="categorie" name="categorie">
                                                            <span class="form-check-label">Catégorie</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"  id="sexe" name="sexe">
                                                            <span class="form-check-label">Sexe</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"  id="cin" name="cin">
                                                            <span class="form-check-label">Cin</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <div class="form-label">Les inscrits archivés :</div>
                                                    <div>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"  id="archive" name="archive">
                                                            <span class="form-check-label">Inclure les inscrits archivés dans les calculs</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <div class="form-label">Les modules sur mesure :</div>
                                                    <div>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"  id="sur_mesure" name="sur_mesure">
                                                            <span class="form-check-label">Possède des modules sur mesure</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <div class="form-label">Champs supplémentaires pour les inscriptions :</div>
                                                    <div>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"  id="cmi_time" name="cmi_time">
                                                            <span class="form-check-label">Temps d'engagement</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"  id="calculated_time" name="calculated_time">
                                                            <span class="form-check-label">Temps calculé</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox"  id="recommended_time" name="recommended_time">
                                                            <span class="form-check-label">Temps pédagogique recommandé</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Date début contrat :</label>
                                                    <div class="input-icon mb-2">
                                                        <input class="form-control" placeholder="Séléctionner votre date"
                                                            id="contract_start_date" value="" name="contract_start_date" />
                                                        <span
                                                            class="input-icon-addon"><!-- Download SVG icon from http://tabler-icons.io/i/calendar -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path
                                                                    d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                                <path d="M16 3v4" />
                                                                <path d="M8 3v4" />
                                                                <path d="M4 11h16" />
                                                                <path d="M11 15h1" />
                                                                <path d="M12 15v3" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer form-footer text-end">
                                    <div class="mt-3">
                                        <a href="{{ route('admin.tenants.index') }}"
                                            class="btn btn-link link-secondary text-dark" type="button">
                                            Annuler
                                        </a>
                                        <button type="submit" class="btn btn-red ms-auto">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                height="24" viewBox="0 0 24 24" stroke-width="2"
                                                stroke="currentColor" fill="none" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M12 5l0 14" />
                                                <path d="M5 12l14 0" />
                                            </svg>
                                            Sauvegarder vos données
                                        </button>
                                    </div>
                                </div>
                            </form>
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
    <script src="{{ asset('dist/libs/litepicker/dist/litepicker.js') }}" defer></script>
    <script src="{{ asset('dist/libs/tom-select/dist/js/tom-select.base.min.js') }}" defer></script>
@stop
@section('ideoreport_js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
                window.Litepicker && (new Litepicker({
                    element: document.getElementById('contract_start_date'),
                    buttonText: {
                        previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>`,
                        nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>`,
                    },
                }));
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('docebo_org_id'), {
                copyClassesToDropdown: false,
                dropdownParent: 'body',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('zendesk_org_id'), {
                copyClassesToDropdown: false,
                dropdownParent: 'body',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });
    </script>
    <script>

        var cbCmi = document.getElementById('cmi_time');
        var cbCalculated = document.getElementById('calculated_time');
        var cbRecommended = document.getElementById('recommended_time');
        cbCalculated.addEventListener('change', function() {
            if (cbCalculated.checked) {
                cbCmi.checked = true;
                cbCalculated.checked = true;
                cbRecommended.checked = true;
            } else {
                cbCalculated.checked = false;;
            }
        });

        cbCmi.addEventListener('change', function () {
            if (!cbCmi.checked) {
                cbCalculated.checked = false;
            }
        });

        cbRecommended.addEventListener('change', function () {
            if (!cbRecommended.checked) {
                cbCalculated.checked = false;
            }
        });

    </script>
@stop
