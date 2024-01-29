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
                        Projets
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
                                    <h2>Créer un nouveau projet</h2>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.tenants.projects.store' , ['tenant' => $tenant->id]) }}" id="ajaxForm" role="form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Nom du projet') }}</label>
                                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                                    value="{{ old('name') }}" placeholder="project name"  autocomplete="name" autofocus>
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Liste des groupes') }}</label>
                                                <select type="text" class="form-select" placeholder="Select project groups" id="select-groups" name="groups[]" value="" multiple>
                                                    @foreach($groups as $group)
                                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('visibility')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-footer text-end">
                                        <div class="mt-3">
                                            <a href="{{ route('admin.tenants.projects',['tenant' => $tenant->id]) }}" class="btn btn-link link-secondary text-dark" type="button">
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
    </div>
@stop
@section('footer')
    @include('central.partials.footer.bottom')
@stop
@section('ideoreport_libs')
    <script src="{{ asset('dist/libs/tom-select/dist/js/tom-select.base.min.js') }}" defer></script>
@stop
@section('ideoreport_js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('select-groups'), {
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
