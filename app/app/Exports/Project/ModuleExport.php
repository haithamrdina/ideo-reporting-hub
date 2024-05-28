<?php

namespace App\Exports\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModuleExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;
    protected $projectId;
    protected $datedebut;
    protected $datefin;
    public function __construct(string $projectId, $datedebut = null, $datefin = null)
    {
        $this->projectId = $projectId;
        $this->datedebut = $datedebut;
        $this->datefin = $datefin;
    }
    public function sheets(): array
    {
        $sheets = [
            new CegosExport($this->projectId, $this->datedebut, $this->datefin),
            new EniExport($this->projectId, $this->datedebut, $this->datefin),
            new SpeexExport($this->projectId, $this->datedebut, $this->datefin),
            new MoocExport($this->projectId, $this->datedebut, $this->datefin),
        ];
        $sur_mesure = config('tenantconfigfields.sur_mesure');
        if ($sur_mesure == true) {
            $sheets[] = new SmExport($this->projectId, $this->datedebut, $this->datefin);
        }
        return $sheets;
    }

}