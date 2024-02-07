<?php

namespace App\Livewire\Tenant\Plateforme;

use App\Enums\GroupStatusEnum;
use App\Models\Group;
use App\Services\GroupeReportService;
use Livewire\Component;

class Groupe extends Component
{
    public function render()
    {
        $groupes = Group::where('status', GroupStatusEnum::ACTIVE)->get();
        $groupe = Group::where(['status'=> GroupStatusEnum::ACTIVE, 'id' => 1] )->first();
        $groupeReportService = new GroupeReportService();
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
        $statsInscriptionsPerDate = $groupeReportService->getLearnersInscriptionsPerStatDate($contract_start_date_conf,$groupe);
        $statsTimingPerDate = $groupeReportService->getTimingDetailsPerStatDate($contract_start_date_conf,  $enrollfields,$groupe);
        return view('livewire.tenant.plateforme.groupe'  ,[
            'groupes' => $groupes,
            'contract_start_date_conf' => $contract_start_date_conf,
            'enrollfields' => $enrollfields,
            'statDate' => $statDate,
            'statsInscriptionsPerDate' => $statsInscriptionsPerDate,
            'statsTimingPerDate' => $statsTimingPerDate,
        ])->layoutData(['title' => 'Filiales']);
    }

}
