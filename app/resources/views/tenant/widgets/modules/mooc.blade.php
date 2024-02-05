@php
    $cmi_time_conf = config('tenantconfigfields.enrollmentfields.cmi_time');
    $calculated_time_conf = config('tenantconfigfields.enrollmentfields.calculated_time');
    $recommended_time_conf = config('tenantconfigfields.enrollmentfields.recommended_time');
@endphp
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
                            <div class="h5" id="insMcW">{{ $moocStats['statMooc']['waiting'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 card  border-0">
                        <div class="card-body bg-danger  text-white">
                            Demandes traités
                            <div class="h5" id="insMcND">{{ $moocStats['statMooc']['enrolled'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 card  border-0">
                        <div class="card-body bg-warning  text-white">
                            En cours
                            <div class="h5" id="insMcP">{{ $moocStats['statMooc']['in_progress'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 card  border-0">
                        <div class="card-body bg-success text-white">
                            Terminé
                            <div class="h5" id="insMcT">{{ $moocStats['statMooc']['completed'] }}</div>
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
                            <div class="h5" id="sessionMc">{{ $moocStats['statMoocTimes']['total_session_time'] }}</div>
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
                            <div class="h5" id="cmiMc">{{ $moocStats['statMoocTimes']['total_cmi_time'] }}</div>
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
                            <div class="h5" id="tcMc">{{ $moocStats['statMoocTimes']['total_calculated_time'] }}</div>
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
                            <div class="h5" id="trMc">{{ $moocStats['statMoocTimes']['total_recommended_time'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div id="chart-moocs">
                    {{  $moocStats['moocCharts']  !=null  ?  $moocStats['moocCharts']->render() : ''}}
                </div>
            </div>
        </div>
    </div>
</div>
