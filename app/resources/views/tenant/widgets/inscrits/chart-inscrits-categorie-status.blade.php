<div class="card h-100">
    <div class="card-body">
        <div class="d-flex">
            <h3 class="card-title">Répartition des inscrits par catégorie et statut :</h3>
        </div>
        <div class="col-auto">
            <div id="chart-completion-tasks-9">
                {{ $chartsInscrits['chartInscritPerCategorieAndStatus'] !=null  ? $chartsInscrits['chartInscritPerCategorieAndStatus']->container() : ''}}
            </div>
        </div>
    </div>
</div>
