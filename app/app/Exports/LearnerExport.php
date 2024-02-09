<?php

namespace App\Exports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LearnerExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    public function sheets(): array{
        $sheets = [
            new ActiveLearnerExport(),
            new InactiveLearnerExport(),
            new ArchiveLearnerExport(),
        ];
        return $sheets;
    }


}
