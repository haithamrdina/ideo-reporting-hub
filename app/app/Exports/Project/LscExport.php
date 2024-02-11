<?php

namespace App\Exports\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LscExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    protected $projectId;
    public function __construct(string $projectId)
    {
        $this->projectId = $projectId;
    }
    public function sheets(): array{
        $sheets = [
            new TicketExport($this->projectId),
            new CallExport($this->projectId)
        ];
        return $sheets;
    }
}
