@php
    $sur_mesure = config('tenantconfigfields.sur_mesure');
@endphp
<div class="modal modal-blur fade" id="modal-session-time" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="icon icon-tabler icon-tabler-help-octagon icon mb-2 text-danger icon-lg" width="32"
                    height="32" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path
                        d="M9.103 2h5.794a3 3 0 0 1 2.122 .879l4.101 4.1a3 3 0 0 1 .88 2.125v5.794a3 3 0 0 1 -.879 2.122l-4.1 4.101a3 3 0 0 1 -2.123 .88h-5.795a3 3 0 0 1 -2.122 -.88l-4.101 -4.1a3 3 0 0 1 -.88 -2.124v-5.794a3 3 0 0 1 .879 -2.122l4.1 -4.101a3 3 0 0 1 2.125 -.88z">
                    </path>
                    <path d="M12 16v.01"></path>
                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                </svg>
                <h3>TEMPS DE SESSION</h3>
                <div class="text-muted">Il désigne la durée cumulée pendant laquelle un apprenant est connecté à une
                    formation. Par exemple, un apprenant peut accumuler 10
                    heures sur une formation donnée. Cette durée englobe également les moments où l'apprenant prend une
                    pause sans se déconnecter.</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="javascript:void(0)" class="btn w-100" data-bs-dismiss="modal">
                                Accéder au tableau de bord
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($cmi_time_conf == true)
    <div class="modal modal-blur fade" id="modal-engaged-time" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-status bg-bitbucket"></div>
                <div class="modal-body text-center py-4">
                    <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="icon icon-tabler icon-tabler-help-octagon icon mb-2 text-bitbucket icon-lg"
                        width="32" height="32" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path
                            d="M9.103 2h5.794a3 3 0 0 1 2.122 .879l4.101 4.1a3 3 0 0 1 .88 2.125v5.794a3 3 0 0 1 -.879 2.122l-4.1 4.101a3 3 0 0 1 -2.123 .88h-5.795a3 3 0 0 1 -2.122 -.88l-4.101 -4.1a3 3 0 0 1 -.88 -2.124v-5.794a3 3 0 0 1 .879 -2.122l4.1 -4.101a3 3 0 0 1 2.125 -.88z">
                        </path>
                        <path d="M12 16v.01"></path>
                        <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                    </svg>
                    <h3>TEMPS D'ENGAGEMENT</h3>
                    <div class="text-muted">C'est la durée pendant laquelle l'apprenant interagit activement avec la
                        formation. Cela pourrait inclure le temps passé à regarder des
                        vidéos, et à répondre à des activités.</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="javascript:void(0)" class="btn w-100" data-bs-dismiss="modal">
                                    Accéder au tableau de bord
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if ($calculated_time_conf == true)
    <div class="modal modal-blur fade" id="modal-calculated-time" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-status bg-yellow"></div>
                <div class="modal-body text-center py-4">
                    <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="icon icon-tabler icon-tabler-help-octagon icon mb-2 text-yellow icon-lg" width="32"
                        height="32" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path
                            d="M9.103 2h5.794a3 3 0 0 1 2.122 .879l4.101 4.1a3 3 0 0 1 .88 2.125v5.794a3 3 0 0 1 -.879 2.122l-4.1 4.101a3 3 0 0 1 -2.123 .88h-5.795a3 3 0 0 1 -2.122 -.88l-4.101 -4.1a3 3 0 0 1 -.88 -2.124v-5.794a3 3 0 0 1 .879 -2.122l4.1 -4.101a3 3 0 0 1 2.125 -.88z">
                        </path>
                        <path d="M12 16v.01"></path>
                        <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                    </svg>
                    <h3>TEMPS CALCULÉ</h3>
                    <div class="text-muted">Il s'agit du temps pendant lequel l'apprenant est réellement concentré sur
                        le contenu d'apprentissage, sans compter les moments où il pourrait
                        laisser une vidéo tourner en arrière-plan ou être inactif sur la plateforme, ce temps est
                        calculé en se basant sur 3 indicateurs (Temps d’engagement, temps
                        pédagogique recommandé et le statut d’inscription).</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="javascript:void(0)" class="btn w-100" data-bs-dismiss="modal">
                                    Accéder au tableau de bord
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if ($recommended_time_conf == true)
    <div class="modal modal-blur fade" id="modal-recommended-time" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-status bg-green"></div>
                <div class="modal-body text-center py-4">
                    <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="icon icon-tabler icon-tabler-help-octagon icon mb-2 text-green icon-lg" width="32"
                        height="32" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path
                            d="M9.103 2h5.794a3 3 0 0 1 2.122 .879l4.101 4.1a3 3 0 0 1 .88 2.125v5.794a3 3 0 0 1 -.879 2.122l-4.1 4.101a3 3 0 0 1 -2.123 .88h-5.795a3 3 0 0 1 -2.122 -.88l-4.101 -4.1a3 3 0 0 1 -.88 -2.124v-5.794a3 3 0 0 1 .879 -2.122l4.1 -4.101a3 3 0 0 1 2.125 -.88z">
                        </path>
                        <path d="M12 16v.01"></path>
                        <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                    </svg>
                    <h3>TEMPS PÉDAGOGIQUE RECOMMANDÉ</h3>
                    <div class="text-muted">C'est la durée suggérée par les concepteurs du cours pour compléter le
                        module. Par exemple, un module CEGOS est
                        conçu pour être terminé en 2 heures, même si les apprenants peuvent choisir de le compléter plus
                        rapidement ou plus lentement.</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="javascript:void(0)" class="btn w-100" data-bs-dismiss="modal">
                                    Accéder au tableau de bord
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- <div class="modal modal-blur fade" id="modal-report-plateforme" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('tenant.plateforme.export') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau Rapport</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Liste des rapports</label>
                                <select class="form-select" name="rapport">
                                    <option value="" disabled selected>Choisir votre rapport</option>
                                    <optgroup label="Inscriptions">
                                        <option value="active">Rapport des inscrits actifs</option>
                                        <option value="inactive">Rapport des inscrits inactifs</option>
                                        <option value="connexion">Rapport des connexions</option>
                                    </optgroup>
                                    <optgroup label="Formation Transverse">
                                        <option value="transverse">Rapport des inscriptions au formation
                                            transverse</option>
                                    </optgroup>
                                    <optgroup label="Modules">
                                        <option value="cegos">Rapport des inscriptions au formation
                                            softskills</option>
                                        <option value="eni">Rapport des inscriptions au formation digital
                                        </option>
                                        <option value="speex">Rapport des inscriptions au formation langue
                                        </option>
                                        @if ($sur_mesure == true)
                                            <option value="sm">Rapport des inscriptions au formation sur
                                                mesure</option>
                                        @endif
                                        <option value="mooc">Rapport des inscriptions au mooc</option>
                                    </optgroup>
                                    <optgroup label="Learner Success Center">
                                        <option value="tickets">Rapport des tickets</option>
                                        <option value="calls">Rapport des appels</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Date début</label>
                                <input type="date" class="form-control" name="dateDebut">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" class="form-control" name="dateFin">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link  link-secondary" data-bs-dismiss="modal">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-red ms-auto" data-bs-dismiss="modal">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Générer votre rapport
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> --}}
{{-- <div class="modal modal-blur fade" id="modal-report-plateforme" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="reportForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau Rapport</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Liste des rapports</label>
                                <select class="form-select" name="rapport">
                                    <option value="" disabled selected>Choisir votre rapport</option>
                                    <optgroup label="Inscriptions">
                                        <option value="active">Rapport des inscrits actifs</option>
                                        <option value="inactive">Rapport des inscrits inactifs</option>
                                        <option value="connexion">Rapport des connexions</option>
                                    </optgroup>
                                    <optgroup label="Formation Transverse">
                                        <option value="transverse">Rapport des inscriptions au formation transverse
                                        </option>
                                    </optgroup>
                                    <optgroup label="Modules">
                                        <option value="cegos">Rapport des inscriptions au formation softskills
                                        </option>
                                        <option value="eni">Rapport des inscriptions au formation digital</option>
                                        <option value="speex">Rapport des inscriptions au formation langue</option>
                                        @if ($sur_mesure == true)
                                            <option value="sm">Rapport des inscriptions au formation sur mesure
                                            </option>
                                        @endif
                                        <option value="mooc">Rapport des inscriptions au mooc</option>
                                    </optgroup>
                                    <optgroup label="Learner Success Center">
                                        <option value="tickets">Rapport des tickets</option>
                                        <option value="calls">Rapport des appels</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Date début</label>
                                <input type="date" class="form-control" name="dateDebut">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" class="form-control" name="dateFin">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-red ms-auto">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Générer votre rapport
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> --}}
<div class="modal modal-blur fade" id="modal-report-plateforme" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="reportForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau Rapport</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Liste des rapports</label>
                                <select class="form-select" name="rapport">
                                    <option value="" disabled selected>Choisir votre rapport</option>
                                    <option value="inscriptions">Rapport des inscriptions</option>
                                    <option value="cegos">Rapport des formations softskills</option>
                                    <option value="eni">Rapport des formations digitals</option>
                                    <option value="speex">Rapport des formations langues</option>
                                    @if ($sur_mesure)
                                        <option value="sm">Rapport des formations sur mesure</option>
                                    @endif
                                    <option value="mooc">Rapport des formations moocs</option>
                                    <option value="transverse">Rapport de formation tranverse</option>
                                    <option value="lsc">Rapport de learner success center</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Date début</label>
                                <input type="date" class="form-control" name="dateDebut">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" class="form-control" name="dateFin">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-red ms-auto">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                            viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Générer votre rapport
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@isset($group)
    <div class="modal modal-blur fade" id="modal-report-group" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('tenant.group.export') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" name="group_id" value={{ $group->id }}>
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau Rapport</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Liste des rapports</label>
                                    <select class="form-select" name="rapport">
                                        <option value="" disabled selected>Choisir votre rapport</option>
                                        <optgroup label="Inscriptions">
                                            <option value="active">Rapport des inscrits actifs</option>
                                            <option value="inactive">Rapport des inscrits inactifs</option>
                                        </optgroup>
                                        <optgroup label="Formation Transverse">
                                            <option value="transverse">Rapport des inscriptions au formation
                                                transverse</option>
                                        </optgroup>
                                        <optgroup label="Modules">
                                            <option value="cegos">Rapport des inscriptions au formation
                                                softskills</option>
                                            <option value="eni">Rapport des inscriptions au formation digital
                                            </option>
                                            <option value="speex">Rapport des inscriptions au formation langue
                                            </option>
                                            @if ($sur_mesure == true)
                                                <option value="sm">Rapport des inscriptions au formation sur
                                                    mesure</option>
                                            @endif
                                            <option value="mooc">Rapport des inscriptions au mooc</option>
                                        </optgroup>
                                        <optgroup label="Learner Success Center">
                                            <option value="tickets">Rapport des tickets</option>
                                            <option value="calls">Rapport des appels</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Date début</label>
                                    <input type="date" class="form-control" name="dateDebut">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Date fin</label>
                                    <input type="date" class="form-control" name="dateFin">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-link  link-secondary" data-bs-dismiss="modal">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-red ms-auto" data-bs-dismiss="modal">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Générer votre rapport
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endisset
@isset($project)
    <div class="modal modal-blur fade" id="modal-report-project" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('tenant.project.export') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" name="project_id" value={{ $project->id }}>
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau Rapport</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Liste des rapports</label>
                                    <select class="form-select" name="rapport">
                                        <option value="" disabled selected>Choisir votre rapport</option>
                                        <optgroup label="Inscriptions">
                                            <option value="active">Rapport des inscrits actifs</option>
                                            <option value="inactive">Rapport des inscrits inactifs</option>
                                        </optgroup>
                                        <optgroup label="Formation Transverse">
                                            <option value="transverse">Rapport des inscriptions au formation
                                                transverse</option>
                                        </optgroup>
                                        <optgroup label="Modules">
                                            <option value="cegos">Rapport des inscriptions au formation
                                                softskills</option>
                                            <option value="eni">Rapport des inscriptions au formation digital
                                            </option>
                                            <option value="speex">Rapport des inscriptions au formation langue
                                            </option>
                                            @if ($sur_mesure == true)
                                                <option value="sm">Rapport des inscriptions au formation sur
                                                    mesure</option>
                                            @endif
                                            <option value="mooc">Rapport des inscriptions au mooc</option>
                                        </optgroup>
                                        <optgroup label="Learner Success Center">
                                            <option value="tickets">Rapport des tickets</option>
                                            <option value="calls">Rapport des appels</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Date début</label>
                                    <input type="date" class="form-control" name="dateDebut">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Date fin</label>
                                    <input type="date" class="form-control" name="dateFin">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-link  link-secondary" data-bs-dismiss="modal">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-red ms-auto" data-bs-dismiss="modal">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Générer votre rapport
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endisset
