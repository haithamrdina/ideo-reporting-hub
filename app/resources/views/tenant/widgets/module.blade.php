@php
    $sur_mesure = config('tenantconfigfields.sur_mesure');
@endphp
<div class="col-md-12">
    <div class="card">
        <div class="card-header {{ request()->routeIs('tenant.plateforme.home') || request()->routeIs('tenant.project.home') || request()->routeIs('tenant.group.home') ? '' : 'my-4' }}">
            <div class="ribbon ribbon-start bg-bleu h2">
                Modules
            </div>
            @if( request()->routeIs('tenant.plateforme.home') || request()->routeIs('tenant.project.home') || request()->routeIs('tenant.group.home') )
                <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block">
                    <div class="row g-2">
                        <div class="col-auto">
                            <a href="javascript:void(0)" class="btn  text-black" aria-label="Button" id="btnModulesExport" data-bs-toggle="tooltip" data-bs-placement="top" title="Générer votre rapport">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-file-spreadsheet" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                    <path
                                        d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                    </path>
                                    <path d="M8 11h8v7h-8z"></path>
                                    <path d="M8 15h8"></path>
                                    <path d="M11 11v7"></path>
                                </svg>
                                Générer votre rapport
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-body p-2">
            <div class="row row-cards">
                @if ($sur_mesure == true)
                    <div class="col-lg-12">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">Statistique des module sur mesure</h3>
                                <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block d-none">
                                    <div class="row g-2">
                                        <div class="col">
                                            <div class="form-group">
                                                <select type="text" class="form-select" id="select-sms" value=""></select>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="javascript:void(0)" class="btn btn-icon" aria-label="Button" id="btnSMReload" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer le filtre">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="container container-slim py-4 d-none" id="loaderSM">
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
                                <div class="row" id="contentSM">
                                    <div class="col-lg-12 text-center mb-3">
                                        <div class="row">
                                            <div class="col-md-4 card  border-0">
                                                <div class="card-body bg-success pt-2 text-white">
                                                    Terminé
                                                    <div class="h5" id="insSmT">******</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 card  border-0">
                                                <div class="card-body bg-warning pt-2 text-white">
                                                    En cours
                                                    <div class="h5" id="insSmP">******</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 card  border-0">
                                                <div class="card-body bg-danger pt-2 text-white">
                                                    Non démarré
                                                    <div class="h5" id="insSmND">******</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div id="chart-sm"></div>
                                    </div>
                                    <div class="col-lg-5  text-center">
                                        <div class="row fw-bolder">
                                            <div class="col-md-12 card border-0 mb-1">
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-session-time">
                                                    <div class="ribbon ribbon-top bg-red">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                        </svg>
                                                    </div>
                                                </a>
                                                <div class="card-body bg-danger-lt pt-2 text-black">
                                                    Temps de session
                                                    <div class="h5" id="sessionSm">**h **min **s.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 card border-0 mb-1 opacity-10">
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-engaged-time">
                                                    <div class="ribbon ribbon-top bg-bitbucket">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                        </svg>
                                                    </div>
                                                </a>
                                                <div class="card-body bg-bitbucket-lt pt-2 text-black">
                                                    Temps d'engagement
                                                    <div class="h5" id="cmiSm">**h **min **s.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 card border-0 mb-1 opacity-10">
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-calculated-time">
                                                    <div class="ribbon ribbon-top bg-yellow">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                        </svg>
                                                    </div>
                                                </a>
                                                <div class="card-body bg-yellow-lt pt-2 text-black">
                                                    Temps calculé
                                                    <div class="h5" id="tcSm">**h **min **s.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 card border-0 mb-1 opacity-10">
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-recommended-time">
                                                    <div class="ribbon ribbon-top bg-green">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                        </svg>
                                                    </div>
                                                </a>
                                                <div class="card-body bg-green-lt pt-2 text-black">
                                                    Temps pédagogique recommandé
                                                    <div class="h5" id="trSm">**h **min **s.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                <h3 class="card-title">Statistique du formation softskills.</h3>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 text-center mb-3">
                                    <div class="row">
                                        <div class="col-md-4 card  border-0">
                                            <div class="card-body bg-success pt-2 text-white">
                                                Terminé
                                                <div class="h5" id="insSoftT"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 card  border-0">
                                            <div class="card-body bg-warning pt-2 text-white">
                                                En cours
                                                <div class="h5" id="insSoftP"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 card  border-0">
                                            <div class="card-body bg-danger pt-2 text-white">
                                                Non démarré
                                                <div class="h5" id="insSoftND"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 text-center">
                                    <div class="row fw-bolder">
                                        <div class="col-md-12 card  border-0 mb-1">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-session-time">
                                                <div class="ribbon ribbon-top bg-red">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-danger-lt pt-2 text-black">
                                                Temps de session
                                                <div class="h5" id="sessionSoft"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card  border-0 mb-1 {{ $cmi_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-engaged-time">
                                                <div class="ribbon ribbon-top bg-bitbucket">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-bitbucket-lt pt-2 text-black">
                                                Temps d'engagement
                                                <div class="h5" id="cmiSoft"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card  border-0 mb-1 {{ $calculated_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-calculated-time">
                                                <div class="ribbon ribbon-top bg-yellow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-yellow-lt pt-2 text-black">
                                                Temps calculé
                                                <div class="h5" id="tcSoft"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card  border-0 mb-1 {{ $recommended_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-recommended-time">
                                                <div class="ribbon ribbon-top bg-green">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-green-lt pt-2 text-black">
                                                Temps pédagogique recommandé
                                                <div class="h5" id="trSoft"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div id="chart-softs"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Statistique du formation digital</h3>
                            <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block">
                                <div class="row g-2">
                                    <div class="col">
                                        <div class="form-group">
                                            <select type="text" class="form-select" id="select-enis" value=""></select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0)" class="btn btn-icon" aria-label="Button" id="btnEniReload" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer le filtre">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container container-slim py-4 d-none" id="loaderDG">
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
                            <div class="row" id="contentDG">
                                <div class="col-lg-12 text-center mb-3">
                                    <div class="row">
                                        <div class="col-md-4 card  border-0">
                                            <div class="card-body bg-success pt-2 text-white">
                                                Terminé
                                                <div class="h5" id="insEniT"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 card  border-0">
                                            <div class="card-body bg-warning pt-2 text-white">
                                                En cours
                                                <div class="h5" id="insEniP"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 card  border-0">
                                            <div class="card-body bg-danger pt-2 text-white">
                                                Non démarré
                                                <div class="h5" id="insEniND"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div id="chart-digital"></div>
                                </div>
                                <div class="col-lg-5  text-center">
                                    <div class="row fw-bolder">
                                        <div class="col-md-12 card border-0 mb-1">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-session-time">
                                                <div class="ribbon ribbon-top bg-red">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-danger-lt pt-2 text-black">
                                                Temps de session
                                                <div class="h5" id="sessionEni"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card border-0 mb-1  {{ $cmi_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-engaged-time">
                                                <div class="ribbon ribbon-top bg-bitbucket">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-bitbucket-lt pt-2 text-black">
                                                Temps d'engagement
                                                <div class="h5" id="cmiEni"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card border-0 mb-1  {{ $calculated_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-calculated-time">
                                                <div class="ribbon ribbon-top bg-yellow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-yellow-lt pt-2 text-black">
                                                Temps calculé
                                                <div class="h5" id="tcEni"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card border-0 mb-1  {{ $recommended_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-recommended-time">
                                                <div class="ribbon ribbon-top bg-green">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-green-lt pt-2 text-black">
                                                Temps pédagogique recommandé
                                                <div class="h5" id="trEni"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                <h3 class="card-title">Statistique du formation Langue:</h3>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 text-center  mb-3">
                                    <div class="row">
                                        <div class="col-md-4 card  border-0">
                                            <div class="card-body bg-success pt-2 text-white">
                                                Terminé
                                                <div class="h5" id="insSpeexT"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 card  border-0">
                                            <div class="card-body bg-warning pt-2 text-white">
                                                En cours
                                                <div class="h5" id="insSpeexP"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 card  border-0">
                                            <div class="card-body bg-danger pt-2 text-white">
                                                Non démarré
                                                <div class="h5" id="insSpeexND"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center">
                                    <div class="row fw-bolder">
                                        <div class="col-md-12 card border-0 mb-1">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-session-time">
                                                <div class="ribbon ribbon-top bg-red">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body h-100 bg-danger-lt pt-2 text-black">
                                                Temps de session
                                                <div class="h5" id="sessionSpeex"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card border-0 mb-1 {{ $cmi_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-engaged-time">
                                                <div class="ribbon ribbon-top bg-bitbucket">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body h-100 bg-bitbucket-lt pt-2 text-black">
                                                Temps d'engagement
                                                <div class="h5" id="cmiSpeex"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card border-0 mb-1 {{ $calculated_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-calculated-time">
                                                <div class="ribbon ribbon-top bg-yellow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body h-100 bg-yellow-lt pt-2 text-black">
                                                Temps calculé
                                                <div class="h5" id="tcSpeex"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card border-0 mb-1 {{ $recommended_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-recommended-time">
                                                <div class="ribbon ribbon-top bg-green">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body h-100 bg-green-lt pt-2 text-black">
                                                Temps pédagogique recommandé
                                                <div class="h5" id="trSpeex"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title">Statistique du formation langue par niveau et temps de formation</h3>
                            <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block d-none">
                                <div class="row g-2">
                                    <div class="col">
                                        <div class="form-group">
                                            <select type="text" class="form-select" id="select-langues" placeholder ="Séléctionner votre module" value=""></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container container-slim py-4 d-none" id="loaderLG">
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
                            <div class="row" id="contentLG">
                                <div class="col-lg-12">
                                    <div id="chart-speex"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                <h3 class="card-title">Statistiques du Mooc.</h3>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <div class="row fw-bolder">
                                        <div class="col-md-3 card  border-0">
                                            <div class="card-body bg-info  text-white">
                                                Demandes en attente
                                                <div class="h5" id="insMcW"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 card  border-0">
                                            <div class="card-body bg-danger  text-white">
                                                Demandes traités
                                                <div class="h5" id="insMcND"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 card  border-0">
                                            <div class="card-body bg-warning  text-white">
                                                En cours
                                                <div class="h5" id="insMcP"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 card  border-0">
                                            <div class="card-body bg-success text-white">
                                                Terminé
                                                <div class="h5" id="insMcT"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5  text-center">
                                    <div class="row fw-bolder">
                                        <div class="col-md-12 card border-0 mb-1">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-session-time">
                                                <div class="ribbon ribbon-top bg-red">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-danger-lt pt-2 text-black">
                                                Temps de session
                                                <div class="h5" id="sessionMc"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card border-0 mb-1 {{ $cmi_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-engaged-time">
                                                <div class="ribbon ribbon-top bg-bitbucket">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-bitbucket-lt pt-2 text-black">
                                                Temps d'engagement
                                                <div class="h5" id="cmiMc"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card border-0 mb-1 {{ $calculated_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-calculated-time">
                                                <div class="ribbon ribbon-top bg-yellow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-yellow-lt pt-2 text-black">
                                                Temps calculé
                                                <div class="h5" id="tcMc"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 card border-0 mb-1 {{ $recommended_time_conf != true ? 'opacity-10' : '' }}">
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-recommended-time">
                                                <div class="ribbon ribbon-top bg-green">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                            <div class="card-body bg-green-lt pt-2 text-black">
                                                Temps pédagogique recommandé
                                                <div class="h5" id="trMc"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div id="chart-moocs"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                <h3 class="card-title">Répartition de temps de session,temps d'engagement, temps calculé et le temps pédagogique recommandé par catégorie :</h3>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div id="chart-combination"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
