<?php

namespace App\Livewire\Tenant\Partials\Contents;

use App\Models\Project;
use App\Services\ProjectReportService;
use Livewire\Component;

class StatInscritPerDate extends Component
{
    public $date;
    public $statDateConfig;
    public $statsTimingPerDate;
    public $statsInscriptionsPerDate;
    public $enrollfields;
    public $projects;

    // Listen for the selectedProjectChanged event emitted by the parent component
    protected $listeners = ['selectedProjectChanged'];

    // Method called when the selectedProjectChanged event is received
    public function selectedProjectChanged($projectId)
    {
        $this->fetchProjectData($projectId);
    }

    protected function fetchProjectData($projectId)
    {
        $project = Project::find($projectId);
        $this->projects = Project::all();
        $projectReportService = new ProjectReportService();
        $this->statsInscriptionsPerDate = $projectReportService->getLearnersInscriptionsPerStatDate($this->contract_start_date_conf, $project);
        $this->statsTimingPerDate = $projectReportService->getTimingDetailsPerStatDate($this->contract_start_date_conf,  $this->enrollfields, $project);
    }

    public function render()
    {
        return view('livewire.tenant.partials.contents.stat-inscrit-per-date');
    }
}
