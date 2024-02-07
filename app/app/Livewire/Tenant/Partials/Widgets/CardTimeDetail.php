<?php

namespace App\Livewire\Tenant\Partials\Widgets;

use App\Services\PlateformeReportService;
use Livewire\Component;

class CardTimeDetail extends Component
{
    public $card_color;

    public $bg_color;

    public $text_color;

    public $modal_name;

    public $name;

    public $total_time;

    public $avg_time;

    public $description;



    public function render()
    {
        return view('livewire.tenant.partials.widgets.card-time-detail');
    }
}
