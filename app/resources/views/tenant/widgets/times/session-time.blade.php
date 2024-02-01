
<div class="card h-100 bg-danger-lt">
    <div class="card-body">
        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-session-time">
            <div class="ribbon ribbon-top bg-red">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon-filled" width="32" height="32" viewbox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14.897 1a4 4 0 0 1 2.664 1.016l.165 .156l4.1 4.1a4 4 0 0 1 1.168 2.605l.006 .227v5.794a4 4 0 0 1 -1.016 2.664l-.156 .165l-4.1 4.1a4 4 0 0 1 -2.603 1.168l-.227 .006h-5.795a3.999 3.999 0 0 1 -2.664 -1.017l-.165 -.156l-4.1 -4.1a4 4 0 0 1 -1.168 -2.604l-.006 -.227v-5.794a4 4 0 0 1 1.016 -2.664l.156 -.165l4.1 -4.1a4 4 0 0 1 2.605 -1.168l.227 -.006h5.793zm-2.897 14a1 1 0 0 0 -.993 .883l-.007 .117l.007 .127a1 1 0 0 0 1.986 0l.007 -.117l-.007 -.127a1 1 0 0 0 -.993 -.883zm1.368 -6.673a2.98 2.98 0 0 0 -3.631 .728a1 1 0 0 0 1.44 1.383l.171 -.18a.98 .98 0 0 1 1.11 -.15a1 1 0 0 1 -.34 1.886l-.232 .012a1 1 0 0 0 .111 1.994a3 3 0 0 0 1.371 -5.673z" stroke-width="0" fill="currentColor"></path>
                </svg>
            </div>
        </a>
        <div class="d-flex align-items-center">
            <div class="subheader">Temps de session</div>
        </div>
        <div class="d-flex align-items-baseline">
            <div class="h2 mb-0 me-2" id="session">**h **min **s.</div>
        </div>
        <div class="d-flex align-items-baseline">
            <div class="h5 mb-0 me-2 text-black fw-bolder ">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-math-avg" width="24" height="24" viewbox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 21l18 -18"></path>
                    <path d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"></path>
                </svg>
                <span id="avgsession">**h **min **s.</span>

            </div>
        </div>
    </div>
</div>


<div class="modal modal-blur fade" id="modal-session-time" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-danger"></div>
            <div
                class="modal-body text-center py-4">
                <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-octagon icon mb-2 text-danger icon-lg" width="32" height="32" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9.103 2h5.794a3 3 0 0 1 2.122 .879l4.101 4.1a3 3 0 0 1 .88 2.125v5.794a3 3 0 0 1 -.879 2.122l-4.1 4.101a3 3 0 0 1 -2.123 .88h-5.795a3 3 0 0 1 -2.122 -.88l-4.101 -4.1a3 3 0 0 1 -.88 -2.124v-5.794a3 3 0 0 1 .879 -2.122l4.1 -4.101a3 3 0 0 1 2.125 -.88z"></path>
                    <path d="M12 16v.01"></path>
                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                </svg>
                <h3>TEMPS DE SESSION</h3>
                <div class="text-muted">Il désigne la durée cumulée pendant laquelle un apprenant est connecté à une formation. Par exemple, un apprenant peut accumuler 10
            heures sur une formation donnée. Cette durée englobe également les moments où l'apprenant prend une pause sans se déconnecter.</div>
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
