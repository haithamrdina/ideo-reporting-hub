<?php

namespace App\Livewire\Tenant\Partials\Widgets;

use App\Services\PlateformeReportService;
use Livewire\Component;

class CardInscritsDetail extends Component
{

    public $card_title;

    public $card_data;

    public $statsInscriptionsPerDate;
    public function render()
    {
        return view('livewire.tenant.partials.widgets.card-inscrits-detail');
    }
}
