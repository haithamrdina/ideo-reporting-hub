<?php

namespace App\Livewire\Tenant\Plateforme;

use App\Services\PlateformeReportService;
use Livewire\Component;

class Home extends Component
{
    public $contract_start_date_conf;
    public $enrollfields;
    public $statDate;
    public $statsInscriptionsPerDate;
    public $statsTimingPerDate;

    public function mount(){
        $plateformeReportService = new PlateformeReportService();
        $this->contract_start_date_conf = config('tenantconfigfields.contract_start_date');
        $this->enrollfields = config('tenantconfigfields.enrollmentfields');
        if($this->contract_start_date_conf != null)
        {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d',   $this->contract_start_date_conf);
            $yearOfDate = $date->year;
            $currentYear = now()->year;
            if ($yearOfDate > $currentYear) {
                $this->statDate = $date->format('d-m-') . now()->year;
            } else {
                $this->statDate =  $date->format('d-m-') . (now()->year - 1) ;
            }
        }
        $this->statsInscriptionsPerDate = $plateformeReportService->getLearnersInscriptionsPerStatDate($this->contract_start_date_conf);
        $this->statsTimingPerDate = $plateformeReportService->getTimingDetailsPerStatDate($this->contract_start_date_conf,  $this->enrollfields);
    }

    public function render()
    {
        return view('livewire.tenant.plateforme.home')->layoutData(['title' => 'Plateforme']);
    }
}
