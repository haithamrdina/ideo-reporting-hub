<?php

namespace App\Http\Controllers\Tenant\Plateforme;

use App\Charts\InscritPerCategory;
use App\Charts\InscritPerCategoryAndStatus;
use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Services\PlateformeReportService;

class HomeController extends Controller
{
    /**
     * Show the User dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(InscritPerCategory $chartInscritPerCategorie, InscritPerCategoryAndStatus $chartInscritPerCategoryAndStatus) {

        $plateformeReportService = new PlateformeReportService();

        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $inscritsReportFromStatDate = $plateformeReportService->getInscritsReportForStatDate($contract_start_date_conf, $enrollfields);

        $totalInscrits = Learner::count();
        $totalActives = Learner::where('statut', 'active')->count();
        $totalInactives = Learner::where('statut', 'inactive')->count();
        $totalArchives = Learner::where('statut', 'archive')->count();


        $chartsInscrits = $plateformeReportService->getChartsInscrits($chartInscritPerCategorie,$chartInscritPerCategoryAndStatus,$categorie);

        return view('tenant.plateforme.home' ,compact(
            'inscritsReportFromStatDate',
            'totalInscrits',
            'totalActives',
            'totalInactives',
            'totalArchives',
            'chartsInscrits',
        ));
    }
}


