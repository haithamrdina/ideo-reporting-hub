<?php

namespace App\Livewire\Tenant\Partials\Pages;

use Livewire\Component;

class PageHeader extends Component
{
    public $projects;

    public $groupes;

    public $selectedProject;

    public function selectProject($projectId)
    {
        $this->selectedProject = $projectId;
        $this->emit('projectSelected', $projectId); // Émettre l'événement projectSelected avec l'ID du projet sélectionné
    }

    public function render()
    {
        return view('livewire.tenant.partials.pages.page-header', [
            'projects' => $this->projects,
            'groupes' => $this->groupes,
        ]);
    }
}
