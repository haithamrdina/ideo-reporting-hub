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
                        <a href="javascript:void(0)" class="btn btn-icon" aria-label="Button" id="btnSMReload"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer le filtre">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh"
                                width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-help-octagon-filled" width="32"
                                        height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z"
                                            stroke-width="0" fill="currentColor"></path>
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
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-help-octagon-filled" width="32"
                                        height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z"
                                            stroke-width="0" fill="currentColor"></path>
                                    </svg>
                                </div>
                            </a>
                            <div class="card-body bg-bitbucket-lt pt-2 text-black">
                                Temps d'engagement
                                <div class="h5" id="cmiSm">**h **min **s.</div>
                            </div>
                        </div>
                        <div class="col-md-12 card border-0 mb-1 opacity-10">
                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                data-bs-target="#modal-calculated-time">
                                <div class="ribbon ribbon-top bg-yellow">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-help-octagon-filled" width="32"
                                        height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z"
                                            stroke-width="0" fill="currentColor"></path>
                                    </svg>
                                </div>
                            </a>
                            <div class="card-body bg-yellow-lt pt-2 text-black">
                                Temps calculé
                                <div class="h5" id="tcSm">**h **min **s.</div>
                            </div>
                        </div>
                        <div class="col-md-12 card border-0 mb-1 opacity-10">
                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                data-bs-target="#modal-recommended-time">
                                <div class="ribbon ribbon-top bg-green">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-help-octagon-filled" width="32"
                                        height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z"
                                            stroke-width="0" fill="currentColor"></path>
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
