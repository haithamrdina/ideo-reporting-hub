<?php

namespace App\Livewire\Tenant\Plateforme;

use App\Services\PlateformeReportService;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        $plateformeReportService = new PlateformeReportService();
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
        $statsInscriptionsPerDate = $plateformeReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf);
        $statsTimingPerDate = $plateformeReportService->getTimingDetailsPerStatDate($contract_start_date_conf,  $enrollfields);
        return view('livewire.tenant.plateforme.home' ,[
            'contract_start_date_conf' => $contract_start_date_conf,
            'enrollfields' => $enrollfields,
            'statDate' => $statDate,
            'statsInscriptionsPerDate' => $statsInscriptionsPerDate,
            'statsTimingPerDate' => $statsTimingPerDate,
        ])->layoutData(['title' => 'Plateforme']);
    }
}
