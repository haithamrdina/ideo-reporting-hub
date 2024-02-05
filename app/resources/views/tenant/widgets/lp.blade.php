@php
    $cmi_time_conf = config('tenantconfigfields.enrollmentfields.cmi_time');
    $calculated_time_conf = config('tenantconfigfields.enrollmentfields.calculated_time');
    $recommended_time_conf = config('tenantconfigfields.enrollmentfields.recommended_time');
@endphp
<div class="card">
    <div class="card-header">
        <div class="ribbon ribbon-start bg-green h2">
            Formation transverse
        </div>
        <div class="card-actions btn-actions d-md-block d-sm-block d-lg-block d-none">
            <div class="row g-2">
                <div class="col">
                    <div class="form-group">
                        <select type="text" class="form-select" id="select-lps" value="">
                            <option value="">Séléctionner votre module</option>
                            @foreach($lpStats['lps'] as $lp)
                                <option value="{{ $lp->id }}">{{ $lp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <a href="javascript:void(0)" class="btn btn-icon" aria-label="Button" id="btnFtReload" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer le filtre">
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
                            rapport de formation transverse
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="card-title">Statistiques des inscriptions aux parcours de formation transverse</div>
                </div>
                <div class="row row-cards p-2">
                    <div class="col-md-3">
                        <div class="card h-100 bg-danger-lt">
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-session-time">
                                <div class="ribbon ribbon-top bg-red">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                    </svg>
                                </div>
                            </a>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Temps de session
                                    </div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 mb-0 me-2" id="sessionLp">{{ $lpStats['statLpsTimes']['total_session_time'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 {{ $cmi_time_conf != true ? 'opacity-10' : '' }}">
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
                                    <div class="h1 mb-0 me-2" id="cmiLp">{{ $lpStats['statLpsTimes']['total_cmi_time'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 {{ $calculated_time_conf != true ? 'opacity-10' : '' }}">
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
                                    <div class="h1 mb-0 me-2" id="tcLp">{{ $lpStats['statLpsTimes']['total_calculated_time'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 {{ $recommended_time_conf != true ? 'opacity-10' : '' }}">
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
                                    <div class="h1 mb-0 me-2" id="trLp">{{ $lpStats['statLpsTimes']['total_recommended_time'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card h-100 bg-success">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-black">Terminé</div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 mb-0 me-2 text-black" id="insLpT">{{ $lpStats['statLps']['completed'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card h-100 bg-warning">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-black">En cours</div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 mb-0 me-2 text-black" id="insLpP">{{ $lpStats['statLps']['in_progress'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 bg-warning-subtle">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-black">Avec plus 50% d'avancement</div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 mb-0 me-2 text-black" id="insLpPG">{{ $lpStats['statLps']['in_progress_max'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 bg-warning-lt">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-black">Avec moins 50% d'avancement</div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 mb-0 me-2 text-black" id="insLpPL">{{ $lpStats['statLps']['in_progress_min'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card h-100 bg-danger">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader text-black">Non démarré</div>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 mb-0 me-2 text-black" id="insLpND">{{ $lpStats['statLps']['enrolled'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="position-relative">
                    <div class="col-lg-12 col-xl-12">
                        <div class="card border-0">
                            <div class="card-body">
                                <div id="chart-completion-tasks-6">
                                    {{  $lpStats['lpCharts']  !=null  ?  $lpStats['lpCharts']->render() : ''}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
