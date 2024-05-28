<?php

namespace App\Exports\Group;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ModuleExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;
    protected $datedebut;
    protected $datefin;
    protected $groupId;
    public function __construct(string $groupId, $datedebut = null, $datefin = null)
    {
        $this->groupId = $groupId;
        $this->datedebut = $datedebut;
        $this->datefin = $datefin;
    }
    public function sheets(): array
    {
        $sheets = [
            new CegosExport($this->groupId, $this->datedebut, $this->datefin),
            new EniExport($this->groupId, $this->datedebut, $this->datefin),
            new SpeexExport($this->groupId, $this->datedebut, $this->datefin),
            new MoocExport($this->groupId, $this->datedebut, $this->datefin),
        ];

        $sur_mesure = config('tenantconfigfields.sur_mesure');
        if ($sur_mesure == true) {
            $sheets[] = new SmExport($this->groupId, $this->datedebut, $this->datefin);
        }
        return $sheets;
    }

}