<div class="card h-100">
    <div class="card-body">
        <div class="d-flex">
            <h3 class="card-title">Répartition des inscrits par catégorie :</h3>
        </div>
        <div class="col-auto row">
            <div id="chart-demo-pie">
                {{ $chartsInscrits['chartInscritPerCategorie'] != null ? $chartsInscrits['chartInscritPerCategorie']->container() : '' }}
            </div>
        </div>
    </div>
</div>
