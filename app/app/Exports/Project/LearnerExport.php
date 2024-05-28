<?php

namespace App\Exports\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LearnerExport implements WithMultipleSheets, ShouldQueue
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
            new ActiveLearnerExport($this->projectId, $this->datedebut, $this->datefin),
            new InactiveLearnerExport($this->projectId,$this->datedebut, $this->datefin),

        ];
        $archive = config('tenantconfigfields.archive');
        if ($archive == true && $this->datedebut ==null && $this->datefin == null) {
            $sheets[] = new ArchiveLearnerExport($this->projectId);
        }
        return $sheets;
    }


}