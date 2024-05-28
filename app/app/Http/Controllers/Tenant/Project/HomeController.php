<?php

namespace App\Http\Controllers\Tenant\Project;

use App\Exports\Project\LearnerExport;
use App\Exports\Project\LpExport;
use App\Exports\Project\LscExport;
use App\Exports\Project\ModuleExport;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectReportService;
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
    public function index()
    {
        $projectId = Auth::guard('user')->user()->project_id;
        $project = Project::find($projectId);
        return view('tenant.project.home', compact('project'));
    }

    public function getData($projectId)
    {
        $project = Project::find($projectId);
        $projectReportService = new ProjectReportService();
        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $learnersInscriptionsPerStatDate = $projectReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf, $project);
        $timingDetailsPerStatDate = $projectReportService->getTimingDetailsPerStatDate($contract_start_date_conf, $enrollfields, $project);

        $learnersInscriptions = $projectReportService->getLearnersInscriptions($project);
        $timingDetails = $projectReportService->getTimingDetails($enrollfields, $project);
        $learnersCharts = $projectReportService->getLearnersCharts($categorie, $project);

        $softStats = $projectReportService->getStatSoftskills($enrollfields, $project);
        $digitalStats = $projectReportService->getStatDigital($enrollfields, $project);
        $smStats = $projectReportService->getStatSM($enrollfields, $project);
        $speexStats = $projectReportService->getStatSpeex($enrollfields, $project);
        $moocStats = $projectReportService->getStatMooc($enrollfields, $project);
        $timingChart = $projectReportService->getTimingStats($enrollfields, $project);
        $timingCalculatedChart = $projectReportService->getCalculatedTimingStats($enrollfields, $project);
        $lpStats = $projectReportService->getLpStats($enrollfields, $project);
        $lscStats = $projectReportService->getLscStats($project);

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
            'timingCalculatedChart' => $timingCalculatedChart,
            'lpStats' => $lpStats,
            'lscStats' => $lscStats,
            'smStats' => $smStats
        ]);
    }

    public function getLanguageData($projectId, $selectedLanguage)
    {
        $projectReportService = new ProjectReportService();
        $speexChart = $projectReportService->getStatSpeexChart($projectId, $selectedLanguage);
        return response()->json($speexChart);
    }

    public function getDigitalData($projectId, $selectedDigital)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $projectReportService = new ProjectReportService();

        if ($selectedDigital != "null") {
            $digitalStats = $projectReportService->getStatDigitalPerModule($enrollfields, $selectedDigital, $projectId);
        } else {
            $project = Project::find($projectId);
            $digitalStats = $projectReportService->getStatDigital($enrollfields, $project);
        }
        return response()->json($digitalStats);
    }

    public function getSMData($projectId, $selectedSM)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $projectReportService = new ProjectReportService();
        if ($selectedSM != "null") {
            $smStats = $projectReportService->getStatSMPerModule($enrollfields, $selectedSM, $projectId);
        } else {
            $project = Project::find($projectId);
            $smStats = $projectReportService->getStatSM($enrollfields, $project);
        }
        return response()->json($smStats);
    }

    public function getLpData($projectId, $selectedLp)
    {
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        $projectReportService = new ProjectReportService();

        if ($selectedLp != "null") {
            $digitalStats = $projectReportService->geStatsPerLp($enrollfields, $selectedLp, $projectId);
        } else {
            $project = Project::find($projectId);
            $digitalStats = $projectReportService->getLpStats($enrollfields, $project);
        }
        return response()->json($digitalStats);
    }


    public function getInscritsPerDate(Request $request, $projectId)
    {

        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $projectReportService = new ProjectReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $learnersInscriptions = $projectReportService->getLearnersInscriptionsPerDate($dateStart, $dateEnd, $projectId);
            $timingDetails = $projectReportService->getTimingDetailsPerDate($enrollfields, $dateStart, $dateEnd, $projectId);
            $learnersCharts = $projectReportService->getLearnersChartsPerDate($categorie, $dateStart, $dateEnd, $projectId);
        } else {
            $project = Project::find($projectId);
            $learnersInscriptions = $projectReportService->getLearnersInscriptions($project);
            $timingDetails = $projectReportService->getTimingDetails($enrollfields, $project);
            $learnersCharts = $projectReportService->getLearnersCharts($categorie, $project);
        }
        return response()->json([
            'learnersInscriptions' => $learnersInscriptions,
            'timingDetails' => $timingDetails,
            'learnersCharts' => $learnersCharts,
        ]);
    }

    public function getLscPerDate(Request $request, $projectId)
    {
        $projectReportService = new ProjectReportService();
        if ($request->has('start_date') && $request->has('end_date')) {
            $dateStart = $request->input('start_date');
            $dateEnd = $request->input('end_date');
            $lscStats = $projectReportService->getLscStatsPerDate($dateStart, $dateEnd, $projectId);
        } else {
            $project = Project::find($projectId);
            $lscStats = $projectReportService->getLscStats($project);
        }

        return response()->json($lscStats);
    }

    public function exportInscrits($projectId)
    {
        return Excel::download(new LearnerExport($projectId), 'rapport_des_inscrits.xlsx');
    }

    public function exportModules($projectId)
    {
        return Excel::download(new ModuleExport($projectId), 'rapport_des_modules.xlsx');
    }

    public function exportLps($projectId)
    {
        return Excel::download(new LpExport($projectId), 'rapport_de_formation_transverse.xlsx');
    }

    public function exportLsc($projectId)
    {
        return Excel::download(new LscExport($projectId), 'rapport_learner_success_center.xlsx');
    }

    public function export(Request $request)
    {
        $rapport = $request->input('rapport');
        $dateDebut = $request->input('dateDebut');
        $dateFin = $request->input('dateFin');
        $projectId = $request->input('project_id');
        if ($dateDebut != null && $dateFin != null) {
            switch ($rapport) {
                case 'inscriptions':
                    $excel = Excel::download(new LearnerExport($projectId, $dateDebut, $dateFin), 'rapport_des_inscrits.xlsx');
                    break;
                case 'transverse':
                    $excel = Excel::download(new LpExport($projectId, $dateDebut, $dateFin), 'rapport_de_formation_transverse.xlsx');
                    break;
                case 'modules':
                    $excel = Excel::download(new ModuleExport($projectId, $dateDebut, $dateFin), 'rapport_des_modules.xlsx');
                    ;
                    break;
                case 'lsc':
                    $excel = Excel::download(new LscExport($projectId, $dateDebut, $dateFin), 'rapport_learner_success_center.xlsx');
                    break;
            }
            return $excel;
        } else {
            switch ($rapport) {
                case 'inscriptions':
                    $excel = Excel::download(new LearnerExport($projectId), 'rapport_des_inscrits.xlsx');
                    break;
                case 'transverse':
                    $excel = Excel::download(new LpExport($projectId), 'rapport_de_formation_transverse.xlsx');
                    break;
                case 'modules':
                    $excel = Excel::download(new ModuleExport($projectId), 'rapport_des_modules.xlsx');
                    break;
                case 'lsc':
                    $excel = Excel::download(new LscExport($projectId), 'rapport_learner_success_center.xlsx');
                    break;
            }
            return $excel;
        }
    }
}