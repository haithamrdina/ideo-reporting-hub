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

        $learnersInscriptionsPerStatDate = $plateformeReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf);
        $timingDetailsPerStatDate = $plateformeReportService->getTimingDetailsPerStatDate($contract_start_date_conf,$enrollfields);

        $learnersInscriptions = $plateformeReportService->getLearnersInscriptions();
        $timingDetails = $plateformeReportService->getTimingDetails($enrollfields);
        $learnersCharts = $plateformeReportService->getLearnersCharts2($categorie);

        return view('tenant.plateforme.home' ,compact(
            'learnersInscriptionsPerStatDate',
            'timingDetailsPerStatDate',
            'learnersInscriptions',
            'timingDetails',
            'learnersCharts',
        ));
    }
}


