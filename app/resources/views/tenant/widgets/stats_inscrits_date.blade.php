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
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <div class="h3 mb-0 me-2">Nombre des nouveaux inscrits
                                            <span class="h1 mb-0 me-2" id="inscritsAY">{{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsLearners']['total'] : '******' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <div class="h3 mb-0 me-2">Nombre d'inscrits actifs
                                            <span class="h1 mb-0 me-2" id="actifsAY">{{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsLearners']['active'] : '******' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <div class="h3 mb-0 me-2">Nombre d'inscrits archivés
                                            <span class="h1 mb-0 me-2" id="archiveAY">******</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 bg-danger-lt">
                                <a href="javascript:void(0)" data-bs-toggle="modal"
                                    data-bs-target="#modal-session-time">
                                    <div class="ribbon ribbon-top bg-red">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path  d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                        </svg>
                                    </div>
                                </a>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Temps de session</div>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <div class="h2 mb-0 me-2" id="sessionAY">
                                            {{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsTimes']['total_session_time'] : '******' }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <div class="h5 mb-0 me-2 text-black fw-bolder">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M3 21l18 -18"></path>
                                                <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                            </svg>
                                            <span id="avgsessionAY">{{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsTimes']['avg_session_time'] : '******' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 {{ $cmi_time_conf == false ? 'opacity-10' : '' }}">
                            <div class="card h-100 bg-bitbucket-lt">
                                <a href="javascript:void(0)" data-bs-toggle="modal"
                                    data-bs-target="#modal-engaged-time">
                                    <div class="ribbon ribbon-top bg-bitbucket">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"  stroke-linecap="round" stroke-linejoin="round">
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
                                        <div class="h2 mb-0 me-2" id="cmiAY">
                                            {{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsTimes']['total_cmi_time'] : '******' }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M3 21l18 -18"></path>
                                                <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                            </svg>
                                            <span id="avgcmiAY">{{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsTimes']['avg_cmi_time'] : '******' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 {{ $calculated_time_conf == false ? 'opacity-10' : '' }}">
                            <div class="card h-100 bg-yellow-lt">
                                <a href="javascript:void(0)" data-bs-toggle="modal"
                                    data-bs-target="#modal-calculated-time">
                                    <div class="ribbon ribbon-top bg-yellow">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled"  width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                                        </svg>
                                    </div>
                                </a>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="subheader">Temps Calculé</div>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <div class="h2 mb-0 me-2" id="tcAY">
                                            {{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsTimes']['total_calculated_time'] : '******' }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M3 21l18 -18"></path>
                                                <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                            </svg>
                                            <span id="avgtcAY">{{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsTimes']['avg_calculated_time'] : '******' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 {{ $recommended_time_conf == false ? 'opacity-10' : '' }}">
                            <div class="card h-100 bg-green-lt">
                                <a href="javascript:void(0)" data-bs-toggle="modal"
                                    data-bs-target="#modal-recommended-time">
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
                                        <div class="h2 mb-0 me-2" id="tprAY">
                                            {{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsTimes']['total_recommended_time'] : '******' }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <div class="h5 mb-0 me-2 text-black fw-bolder ">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M3 21l18 -18"></path>
                                                <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                                            </svg>
                                            <span id="avgtprAY">{{ $inscritsReportFromStatDate != null ? $inscritsReportFromStatDate['statsTimes']['avg_recommended_time'] : '******' }}</span>
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
