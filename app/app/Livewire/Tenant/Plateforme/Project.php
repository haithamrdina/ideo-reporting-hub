<?php

namespace App\Livewire\Tenant\Plateforme;

use App\Models\Project as ModelsProject;
use App\Services\ProjectReportService;
use Livewire\Component;

class Project extends Component
{
    public $projects;
    public $contract_start_date_conf;
    public $enrollfields;
    public $statDate;
    public $statsInscriptionsPerDate;
    public $statsTimingPerDate;

    public $selectedProject = null;

    public function mount()
    {
        $this->projects = ModelsProject::all();
        $this->fetchProjectData($this->selectedProject ?? 1);
    }

    protected function fetchProjectData($projectId)
    {
        $project = ModelsProject::find($projectId);
        $this->contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $this->enrollfields = config('tenantconfigfields.enrollmentfields');

        // Fetch data using the project
        $projectReportService = new ProjectReportService();
        $this->statsInscriptionsPerDate = $projectReportService->getLearnersInscriptionsPerStatDate($this->contract_start_date_conf, $project);
        $this->statsTimingPerDate = $projectReportService->getTimingDetailsPerStatDate($this->contract_start_date_conf, $this->enrollfields, $project);
    }

    // Listen for the selectedProjectChanged event emitted by child components
    protected $listeners = ['selectedProjectChanged'];

    public function selectedProjectChanged($projectId)
    {
        $this->selectedProject = $projectId;
        $this->fetchProjectData($projectId);
    }

    public function render()
    {
        return view('livewire.tenant.plateforme.project')->layoutData(['title' => 'Branches']);
    }
}
