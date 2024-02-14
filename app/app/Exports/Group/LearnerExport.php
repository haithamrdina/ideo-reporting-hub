<?php

namespace App\Exports\Group;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LearnerExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    protected $groupId;
    public function __construct(string $groupId)
    {
        $this->groupId = $groupId;
    }

    public function sheets(): array{
        $sheets = [
            new ActiveLearnerExport($this->groupId),
            new InactiveLearnerExport($this->groupId),
        ];
        $archive = config('tenantconfigfields.archive');
        if($archive == true){
            $sheets []= new ArchiveLearnerExport($this->groupId);
        }
        return $sheets;
    }


}
