<div class="card h-100">
    <div class="card-body">
        <div class="d-flex">
            <h3 class="card-title">Répartition des inscrits par catégorie :</h3>
        </div>
        <div class="col-auto">
            <div id="chart-demo-pie">
                {!! $learnersCharts['chartInscritPerCategorie'] != null ? $learnersCharts['chartInscritPerCategorie']->render() : '' !!}
            </div>
        </div>
    </div>
</div>
