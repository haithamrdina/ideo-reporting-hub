<?php

namespace App\Http\Controllers\Tenant\Plateforme;

use App\Enums\CourseStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectReportService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Show the User dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($projectId = null) {
        $project = $projectId ? Project::find($projectId) : Project::find(1);
        $projects = Project::all();
        $projectReportService = new ProjectReportService();
        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $learnersInscriptionsPerStatDate = $projectReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf,$project);
        $timingDetailsPerStatDate = $projectReportService->getTimingDetailsPerStatDate($contract_start_date_conf,$enrollfields,$project);

        $learnersInscriptions = $projectReportService->getLearnersInscriptions($project);
        $timingDetails = $projectReportService->getTimingDetails($enrollfields,$project);
        $learnersCharts = $projectReportService->getLearnersCharts($categorie,$project);

        $softStats = $projectReportService->getStatSoftskills($enrollfields,$project);
        $digitalStats = $projectReportService->getStatDigital($enrollfields,$project);
        $speexStats = $projectReportService->getStatSpeex($enrollfields,$project);
        $moocStats = $projectReportService->getStatMooc($enrollfields,$project);
        $timingChart = $projectReportService->getTimingStats($enrollfields,$project);
        $lpStats = $projectReportService->getLpStats($enrollfields,$project);
        $lscStats = $projectReportService->getLscStats($project);

        return view('tenant.plateforme.project' ,compact(
            'projects',
            'project',
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

    public function updateData($projectId) {

        $project = Project::find($projectId);
        $project = $projectId ? Project::find($projectId) : Project::find(1);
        $projects = Project::all();
        $projectReportService = new ProjectReportService();
        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $categorie = config('tenantconfigfields.userfields.categorie');
        $enrollfields = config('tenantconfigfields.enrollmentfields');

        $learnersInscriptionsPerStatDate = $projectReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf,$project);
        $timingDetailsPerStatDate = $projectReportService->getTimingDetailsPerStatDate($contract_start_date_conf,$enrollfields,$project);

        $learnersInscriptions = $projectReportService->getLearnersInscriptions($project);
        $timingDetails = $projectReportService->getTimingDetails($enrollfields,$project);
        $learnersCharts = $projectReportService->getLearnersCharts($categorie,$project);

        $softStats = $projectReportService->getStatSoftskills($enrollfields,$project);
        $digitalStats = $projectReportService->getStatDigital($enrollfields,$project);
        $speexStats = $projectReportService->getStatSpeex($enrollfields,$project);
        $moocStats = $projectReportService->getStatMooc($enrollfields,$project);
        $timingChart = $projectReportService->getTimingStats($enrollfields,$project);
        $lpStats = $projectReportService->getLpStats($enrollfields,$project);
        $lscStats = $projectReportService->getLscStats($project);


        return view('tenant.plateforme.project', compact(
            'projects',
            'project',
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



