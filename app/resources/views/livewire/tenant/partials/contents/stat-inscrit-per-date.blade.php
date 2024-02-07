<div>
    @if($statDateConfig !== null)
        <div class="col-12 ">
            <div class="row row-cards">
                <div class="col-md-12 col-lg-12">
                    <div class="card card-active">
                        <div class="ribbon ribbon-start bg-red h2">
                            Statistiques des inscriptions effectuées à partir du {{ $date }} .
                        </div>

                        <div class="card-body mt-6">
                            <div class="row row-cards">
                                <div class="col-md-4">
                                    <livewire:tenant.partials.widgets.card-inscrits-detail card_title="Nombre des nouveaux inscrits" :card_data="$statsInscriptionsPerDate['total']" />
                                </div>
                                <div class="col-md-4">
                                    <livewire:tenant.partials.widgets.card-inscrits-detail card_title="Nombre d'inscrits actifs" :card_data="$statsInscriptionsPerDate['active']" />
                                </div>
                                <div class="col-md-4">
                                    <livewire:tenant.partials.widgets.card-inscrits-detail card_title="Nombre d'inscrits inactives" :card_data="$statsInscriptionsPerDate['inactive']" />
                                </div>
                                <div class="col-md-3">
                                    <livewire:tenant.partials.widgets.card-time-detail
                                        card_color="bg-danger-lt"
                                        bg_color="bg-red"
                                        text_color="text-danger"
                                        modal_name="modal-session-time"
                                        name="Temps de sessions"
                                        description="Il désigne la durée cumulée pendant laquelle un apprenant est connecté à une formation. Par exemple, un apprenant peut accumuler 10
                                        heures sur une formation donnée. Cette durée englobe également les moments où l'apprenant prend une pause sans se déconnecter."
                                        :total_time="$statsTimingPerDate['total_session_time']"
                                        :avg_time="$statsTimingPerDate['avg_session_time']"
                                    />
                                </div>

                                <div class="col-md-3 {{ $enrollfields['cmi_time'] == false ? 'opacity-10' : ''  }}">
                                    <livewire:tenant.partials.widgets.card-time-detail
                                        card_color="bg-bitbucket-lt"
                                        bg_color="bg-bitbucket"
                                        text_color="text-bitbucket"
                                        modal_name="modal-engaged-time"
                                        name="Temps d'engagement"
                                        description="C'est la durée pendant laquelle l'apprenant interagit activement avec la formation. Cela pourrait inclure le temps passé à regarder des
                                        vidéos, et à répondre à des activités."
                                        :total_time="$statsTimingPerDate['total_cmi_time']"
                                        :avg_time="$statsTimingPerDate['avg_cmi_time']"
                                    />
                                </div>


                                <div class="col-md-3 {{  $enrollfields['calculated_time'] == false ? 'opacity-10' : ''  }}">
                                    <livewire:tenant.partials.widgets.card-time-detail
                                        card_color="bg-yellow-lt"
                                        bg_color="bg-yellow"
                                        text_color="text-yellow"
                                        modal_name="modal-calculated-time"
                                        name="Temps calculé"
                                        description="Il s'agit du temps pendant lequel l'apprenant est réellement concentré sur le contenu d'apprentissage, sans compter les moments où il pourrait
                                        laisser une vidéo tourner en arrière-plan ou être inactif sur la plateforme, ce temps est calculé en se basant sur 3 indicateurs (Temps d’engagement, temps
                                        pédagogique recommandé et le statut d’inscription)."
                                        :total_time="$statsTimingPerDate['total_calculated_time']"
                                        :avg_time="$statsTimingPerDate['avg_calculated_time']"
                                    />
                                </div>


                                <div class="col-md-3 {{  $enrollfields['recommended_time'] == false ? 'opacity-10' : ''  }}">
                                    <livewire:tenant.partials.widgets.card-time-detail
                                        card_color="bg-green-lt"
                                        bg_color="bg-green"
                                        text_color="text-green"
                                        modal_name="modal-recommended-time"
                                        name="Temps pédagogique recommendé"
                                        description="C'est la durée suggérée par les concepteurs du cours pour compléter le module. Par exemple, un module CEGOS est
                                        conçu pour être terminé en 2 heures, même si les apprenants peuvent choisir de le compléter plus rapidement ou plus lentement."
                                        :total_time="$statsTimingPerDate['total_recommended_time']"
                                        :avg_time="$statsTimingPerDate['avg_recommended_time']"
                                    />
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
