<?php

namespace App\Http\Controllers\Tenant\Plateforme;

use App\Charts\InscritPerCategory;
use App\Charts\InscritPerCategoryAndStatus;
use App\Exports\ActiveLearnerExport;
use App\Exports\LearnerExport;
use App\Exports\LpExport;
use App\Exports\LscExport;
use App\Exports\ModuleExport;
use App\Http\Controllers\Controller;
use App\Services\PlateformeReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Show the User dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        return view('tenant.plateforme.home');
    }

    public function getData(){
        $plateformeReportService = new PlateformeReportService();

        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $learnersInscriptionsPerStatDate = $plateformeReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf);
        $timingDetailsPerStatDate = $plateformeReportService->getTimingDetailsPerStatDate($contract_start_date_conf,$enrollfields);

        $learnersInscriptions = $plateformeReportService->getLearnersInscriptions();
        $timingDetails = $plateformeReportService->getTimingDetails($enrollfields);
        $learnersCharts = $plateformeReportService->getLearnersCharts($categorie);

        $softStats = $plateformeReportService->getStatSoftskills($enrollfields);
        $digitalStats = $plateformeReportService->getStatDigital($enrollfields);
        $smStats = $plateformeReportService->getStatSM($enrollfields);
        $speexStats = $plateformeReportService->getStatSpeex($enrollfields);
        $moocStats = $plateformeReportService->getStatMooc($enrollfields);
        $timingChart = $plateformeReportService->getTimingStats($enrollfields);
        $lpStats = $plateformeReportService->getLpStats($enrollfields);
        $lscStats = $plateformeReportService->getLscStats();

        return response()->json([
            'learnersInscriptionsPerStatDate' => $learnersInscriptionsPerStatDate,
            'timingDetailsPerStatDate' => $timingDetailsPerStatDate,
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
            'softStats' => $softStats,
            'digitalStats' => $digitalStats,
            'smStats' => $smStats,
            'speexStats' => $speexStats,
            'moocStats' => $moocStats,
            'timingChart' => $timingChart,
            'lpStats' => $lpStats,
            'lscStats' => $lscStats
        ]);
    }

    public function getLanguageData($selectedLanguage){
        $plateformeReportService = new PlateformeReportService();
        $speexChart = $plateformeReportService->getStatSpeexChart($selectedLanguage);
        return response()->json($speexChart);
    }

    public function getDigitalData($selectedDigital){
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $plateformeReportService = new PlateformeReportService();
        if($selectedDigital != "null"){
            $digitalStats = $plateformeReportService->getStatDigitalPerModule($enrollfields, $selectedDigital);
        }else{
            $digitalStats = $plateformeReportService->getStatDigital($enrollfields);
        }
        return response()->json($digitalStats);
    }

    public function getSMData($selectedSM){
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $plateformeReportService = new PlateformeReportService();
        if($selectedSM != "null"){
            $smStats = $plateformeReportService->getStatSMPerModule($enrollfields, $selectedSM);
        }else{
            $smStats = $plateformeReportService->getStatSM($enrollfields);
        }
        return response()->json($smStats);
    }

    public function getLpData($selectedLp){
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $plateformeReportService = new PlateformeReportService();
        if($selectedLp != "null"){
            $digitalStats = $plateformeReportService->geStatsPerLp($enrollfields, $selectedLp);
        }else{
            $digitalStats = $plateformeReportService->getLpStats($enrollfields);
        }
        return response()->json($digitalStats);
    }

    public function getInscritsPerDate(Request $request){

        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $plateformeReportService = new PlateformeReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $learnersInscriptions = $plateformeReportService->getLearnersInscriptionsPerDate($dateStart , $dateEnd);
            $timingDetails = $plateformeReportService->getTimingDetailsPerDate($enrollfields, $dateStart , $dateEnd);
            $learnersCharts = $plateformeReportService->getLearnersChartsPerDate($categorie, $dateStart , $dateEnd);
        } else {
            $learnersInscriptions = $plateformeReportService->getLearnersInscriptions();
            $timingDetails = $plateformeReportService->getTimingDetails($enrollfields);
            $learnersCharts = $plateformeReportService->getLearnersCharts($categorie);
        }
        return response()->json([
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
        ]);
    }

    public function getLscPerDate(Request $request){
        $plateformeReportService = new PlateformeReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $lscStats = $plateformeReportService->getLscStatsPerDate($dateStart, $dateEnd);
        } else {
            $lscStats = $plateformeReportService->getLscStats();
        }

        return response()->json($lscStats);
    }

    public function exportInscrits(){
        return Excel::download(new LearnerExport, 'rapport_des_inscrits.xlsx');
    }

    public function exportModules(){
        return Excel::download(new ModuleExport, 'rapport_des_modules.xlsx');
    }

    public function exportLps(){
        return Excel::download(new LpExport, 'rapport_de_formation_transverse.xlsx');
    }

    public function exportLsc(){
        return Excel::download(new LscExport, 'rapport_learner_success_center.xlsx');
    }
}


