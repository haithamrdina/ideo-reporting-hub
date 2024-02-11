<?php

namespace App\Http\Controllers\Tenant\Group;

use App\Exports\Group\LearnerExport;
use App\Exports\Group\LpExport;
use App\Exports\Group\LscExport;
use App\Exports\Group\ModuleExport;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\GroupeReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
     /**
     * Show the User dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $groupId = Auth::guard('user')->user()->group_id;
        $group = Group::find($groupId);
        return view('tenant.group.home', compact('group'));
    }

    public function getData($groupId){
        $group = Group::find($groupId);
        $groupReportService = new GroupeReportService();
        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $learnersInscriptionsPerStatDate = $groupReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf,$group);
        $timingDetailsPerStatDate = $groupReportService->getTimingDetailsPerStatDate($contract_start_date_conf,$enrollfields,$group);

        $learnersInscriptions = $groupReportService->getLearnersInscriptions($group);
        $timingDetails = $groupReportService->getTimingDetails($enrollfields,$group);
        $learnersCharts = $groupReportService->getLearnersCharts($categorie,$group);

        $softStats = $groupReportService->getStatSoftskills($enrollfields,$group);
        $digitalStats = $groupReportService->getStatDigital($enrollfields,$group);
        $speexStats = $groupReportService->getStatSpeex($enrollfields,$group);
        $moocStats = $groupReportService->getStatMooc($enrollfields,$group);
        $timingChart = $groupReportService->getTimingStats($enrollfields,$group);
        $lpStats = $groupReportService->getLpStats($enrollfields,$group);
        $lscStats = $groupReportService->getLscStats($group);

        return response()->json([
            'learnersInscriptionsPerStatDate' => $learnersInscriptionsPerStatDate,
            'timingDetailsPerStatDate' => $timingDetailsPerStatDate,
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
            'softStats' => $softStats,
            'digitalStats' => $digitalStats,
            'speexStats' => $speexStats,
            'moocStats' => $moocStats,
            'timingChart' => $timingChart,
            'lpStats' => $lpStats,
            'lscStats' => $lscStats
        ]);
    }

    public function getLanguageData($groupId,$selectedLanguage){
        $groupReportService = new GroupeReportService();
        $speexChart = $groupReportService->getStatSpeexChart($groupId, $selectedLanguage);
        return response()->json($speexChart);
    }

    public function getDigitalData($groupId, $selectedDigital){
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $groupReportService = new GroupeReportService();

        if($selectedDigital != "null"){
            $digitalStats = $groupReportService->getStatDigitalPerModule($enrollfields, $selectedDigital,$groupId);
        }else{
            $group = Group::find($groupId);
            $digitalStats = $groupReportService->getStatDigital($enrollfields,$group);
        }
        return response()->json($digitalStats);
    }

    public function getLpData($groupId, $selectedLp){
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $groupReportService = new GroupeReportService();

        if($selectedLp != "null"){
            $digitalStats = $groupReportService->geStatsPerLp($enrollfields, $selectedLp, $groupId);
        }else{
            $group = Group::find($groupId);
            $digitalStats = $groupReportService->getLpStats($enrollfields,$group);
        }
        return response()->json($digitalStats);
    }


    public function getInscritsPerDate(Request $request, $groupId){

        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $groupReportService = new GroupeReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $learnersInscriptions = $groupReportService->getLearnersInscriptionsPerDate($dateStart , $dateEnd, $groupId);
            $timingDetails = $groupReportService->getTimingDetailsPerDate($enrollfields, $dateStart , $dateEnd, $groupId);
            $learnersCharts = $groupReportService->getLearnersChartsPerDate($categorie, $dateStart , $dateEnd, $groupId);
        } else {
            $group = Group::find($groupId);
            $learnersInscriptions = $groupReportService->getLearnersInscriptions($group);
            $timingDetails = $groupReportService->getTimingDetails($enrollfields, $group);
            $learnersCharts = $groupReportService->getLearnersCharts($categorie,$group);
        }
        return response()->json([
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
        ]);
    }

    public function getLscPerDate(Request $request, $groupId){
        $groupReportService = new GroupeReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $lscStats = $groupReportService->getLscStatsPerDate($dateStart, $dateEnd, $groupId);
        } else {
            $group = Group::find($groupId);
            $lscStats = $groupReportService->getLscStats($group);
        }

        return response()->json($lscStats);
    }

    public function exportInscrits($groupId){
        return Excel::download(new LearnerExport($groupId), 'rapport_des_inscrits.xlsx');
    }

    public function exportModules($groupId){
        return Excel::download(new ModuleExport($groupId), 'rapport_des_modules.xlsx');
    }

    public function exportLps($groupId){
        return Excel::download(new LpExport($groupId), 'rapport_de_formation_transverse.xlsx');
    }

    public function exportLsc($groupId){
        return Excel::download(new LscExport($groupId), 'rapport_learner_success_center.xlsx');
    }
}
