<?php

namespace App\Livewire\Tenant\Partials\Contents;

use App\Services\PlateformeReportService;
use Livewire\Component;

class StatInscritPerDate extends Component
{
    public $date;
    public $statDateConfig;
    public $statsTimingPerDate;
    public $statsInscriptionsPerDate;
    public $enrollfields;

    public function render()
    {
        return view('livewire.tenant.partials.contents.stat-inscrit-per-date');
    }
}
