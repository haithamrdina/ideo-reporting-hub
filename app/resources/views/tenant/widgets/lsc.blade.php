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
                        <a href="." class="navbar-brand navbar-brand-autodark"><img src="{{ global_asset('static/logo/logo.svg') }}" height="36" alt=""></a>
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
                                    <span class="h1 mb-0 me-2" id="tickets"></span>
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
                                    <span class="h1 mb-0 me-2" id="calls"></span>
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
                                <div id="chart-ticket-pie"></div>
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
                                <div id="chart-calls-sujet-type"></div>
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
                                <div id="chart-calls-statut-type"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
