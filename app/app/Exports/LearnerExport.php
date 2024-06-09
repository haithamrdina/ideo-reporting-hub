<?php

namespace App\Exports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LearnerExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    protected $datedebut;
    protected $datefin;
    public function __construct($datedebut = null, $datefin = null)
    {
        $this->datedebut = $datedebut;
        $this->datefin = $datefin;
    }

    public function sheets(): array
    {
        $archive = config('tenantconfigfields.archive');
        $sheets = [];
        $sheets[] = new ConnexionExport($this->datedebut, $this->datefin);
        $sheets[] = new ActiveLearnerExport($this->datedebut, $this->datefin);
        $sheets[] = new InactiveLearnerExport($this->datedebut, $this->datefin);
        if ($archive == true && $this->datefin == null && $this->datefin == null) {
            $sheets[] = new ArchiveLearnerExport();
        }
        return $sheets;
    }


}
