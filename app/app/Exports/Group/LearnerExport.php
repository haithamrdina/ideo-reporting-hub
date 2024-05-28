<?php

namespace App\Exports\Group;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LearnerExport implements WithMultipleSheets, ShouldQueue
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
            new ActiveLearnerExport($this->groupId, $this->datedebut, $this->datefin),
            new InactiveLearnerExport($this->groupId, $this->datedebut, $this->datefin),
        ];
        $archive = config('tenantconfigfields.archive');
        if ($archive == true && $this->datefin == null && $this->datefin == null) {
            $sheets[] = new ArchiveLearnerExport($this->groupId);
        }
        return $sheets;
    }


}