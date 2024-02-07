<?php

namespace App\Livewire\Tenant\Plateforme;

use App\Models\Project as ModelsProject;
use App\Services\ProjectReportService;
use Livewire\Component;

class Project extends Component
{

    public function render()
    {
        $project =  ModelsProject::find(1);
        $projects = ModelsProject::all();
        $projectReportService = new ProjectReportService();
        $contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $enrollfields = config('tenantconfigfields.enrollmentfields');
        if($contract_start_date_conf != null)
        {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d',   $contract_start_date_conf);
            $yearOfDate = $date->year;
            $currentYear = now()->year;
            if ($yearOfDate > $currentYear) {
                $statDate = $date->format('d-m-') . now()->year;
            } else {
                $statDate =  $date->format('d-m-') . (now()->year - 1) ;
            }
        }
        $statsInscriptionsPerDate = $projectReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf,$project);
        $statsTimingPerDate = $projectReportService->getTimingDetailsPerStatDate($contract_start_date_conf,  $enrollfields,$project);
        return view('livewire.tenant.plateforme.project' ,[
            'projects' => $projects,
            'contract_start_date_conf' => $contract_start_date_conf,
            'enrollfields' => $enrollfields,
            'statDate' => $statDate,
            'statsInscriptionsPerDate' => $statsInscriptionsPerDate,
            'statsTimingPerDate' => $statsTimingPerDate,
        ])->layoutData(['title' => 'Branches']);
    }
}
