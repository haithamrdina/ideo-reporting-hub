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
    public function __construct(string $projectId)
    {
        $this->projectId = $projectId;
    }

    public function sheets(): array{
        $sheets = [
            new ActiveLearnerExport($this->projectId),
            new InactiveLearnerExport($this->projectId),
            new ArchiveLearnerExport($this->projectId),
        ];
        return $sheets;
    }


}
