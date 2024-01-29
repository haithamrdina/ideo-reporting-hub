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
                                            <input id="company_code" type="text" class="form-control @error('company_code') is-invalid @enderror" name="company_code"
                                                value="{{ old('company_code') }}" placeholder="company code"  autocomplete="company_code" autofocus>
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
                                            <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name"
                                                value="{{ old('company_name') }}" placeholder="company name"  autocomplete="company_name" autofocus>
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
                                            <select type="text" class="form-select" placeholder="Select docebo organisation" id="docebo_org_id"
                                                name="docebo_org_id" value="">
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->client_org_id }}">{{ $client->client_org_name }}</option>
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
                                            <select type="text" class="form-select" placeholder="Select zendesk oragnisation" id="zendesk_org_id"
                                                name="zendesk_org_id" value="">
                                                @foreach($organizations as $org)
                                                    <option value="{{ $org->zendesk_org_id }}">{{ $org->zendesk_org_name }}</option>
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
                                                <input id="subdomain" type="text"class="form-control @error('subdomain') is-invalid @enderror"  name="subdomain" value="{{ old('subdomain') }}"  placeholder="subdomain" autocomplete="subdomain" autofocus>
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
                                        <div class="mb-3">
                                            <label class="form-label">Paramètres</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-check">
                                                <input class="form-check-input" type="checkbox" name="cmi">
                                                <span class="form-check-label">
                                                    Afficher le temps d'engagement
                                                </span>
                                                <span class="form-check-description">
                                                    C'est la durée pendant laquelle l'apprenant interagit activement avec la formation. Cela pourrait inclure le temps passé à regarder des vidéos, et à répondre à des activités.
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-check">
                                                <input class="form-check-input" type="checkbox" name="calculated">
                                                <span class="form-check-label">
                                                    Afficher le temps calculé
                                                </span>
                                                <span class="form-check-description">
                                                    Il s'agit du temps pendant lequel l'apprenant est réellement concentré sur le contenu d'apprentissage, sans compter les moments où il pourrait laisser une vidéo tourner en arrière-plan ou être inactif sur la plateforme, ce temps est calculé en se basant sur 3 indicateurs (Temps d’engagement, temps pédagogique recommandé et le statut d’inscription).
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-check">
                                                <input class="form-check-input" type="checkbox" name="recommended">
                                                <span class="form-check-label">
                                                    Afficher le temps pédagogique recommandé
                                                </span>
                                                <span class="form-check-description">
                                                    C'est la durée suggérée par les concepteurs du cours pour compléter le module. Par exemple, un module CEGOS est conçu pour être terminé en 2 heures, même si les apprenants peuvent choisir de le compléter plus rapidement ou plus lentement.
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Date début contrat</label>
                                            <div class="input-icon mb-2">
                                                <input class="form-control " placeholder="yyyy-mm-dd" id="datepicker-icon" value="" name="date_contrat"/>
                                                <span class="input-icon-addon"><!-- Download SVG icon from http://tabler-icons.io/i/calendar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-footer text-end">
                                    <div class="mt-3">
                                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-link link-secondary text-dark" type="button">
                                            Annuler
                                        </a>
                                        <button type="submit" class="btn btn-red ms-auto">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                element: document.getElementById('datepicker-default'),
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
@stop
