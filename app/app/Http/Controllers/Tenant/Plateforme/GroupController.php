<?php

namespace App\Http\Controllers\Tenant\Plateforme;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\GroupeReportService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
     /**
     * Show the User dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
       $groupeReportService = new GroupeReportService();
       $groupe = Group::find(1);
        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $learnersInscriptionsPerStatDate =$groupeReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf,$groupe);
        $timingDetailsPerStatDate =$groupeReportService->getTimingDetailsPerStatDate($contract_start_date_conf,$enrollfields,$groupe);

        $learnersInscriptions =$groupeReportService->getLearnersInscriptions($groupe);
        $timingDetails =$groupeReportService->getTimingDetails($enrollfields,$groupe);
        $learnersCharts =$groupeReportService->getLearnersCharts($categorie,$groupe);

        $softStats =$groupeReportService->getStatSoftskills($enrollfields,$groupe);
        $digitalStats =$groupeReportService->getStatDigital($enrollfields,$groupe);
        $speexStats =$groupeReportService->getStatSpeex($enrollfields,$groupe);
        $moocStats =$groupeReportService->getStatMooc($enrollfields,$groupe);
        $timingChart =$groupeReportService->getTimingStats($enrollfields,$groupe);
        $lpStats =$groupeReportService->getLpStats($enrollfields,$groupe);
        $lscStats =$groupeReportService->getLscStats($groupe);

        return view('tenant.plateforme.group' ,compact(
            'learnersInscriptionsPerStatDate',
            'timingDetailsPerStatDate',
            'learnersInscriptions',
            'timingDetails',
            'learnersCharts',
            'softStats',
            'digitalStats',
            'speexStats',
            'moocStats',
            'timingChart',
            'lpStats',
            'lscStats'
        ));
    }
}
