<?php

namespace App\Livewire\Tenant\Partials\Pages;

use Livewire\Component;

class PageHeader extends Component
{
    public $projects;

    public $groupes;

    public function render()
    {
        return view('livewire.tenant.partials.pages.page-header');
    }
}
