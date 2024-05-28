<?php

namespace App\Exports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModuleExport implements WithMultipleSheets, ShouldQueue
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
        $sheets = [
            new CegosExport($this->datedebut, $this->datefin),
            new EniExport($this->datedebut, $this->datefin),
            new SpeexExport($this->datedebut, $this->datefin),
            new MoocExport($this->datedebut, $this->datefin),
        ];
        $sur_mesure = config('tenantconfigfields.sur_mesure');
        if ($sur_mesure == true) {
            $sheets[] = new SmExport();
        }
        return $sheets;
    }

}