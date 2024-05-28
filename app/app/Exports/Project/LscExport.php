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
            new TicketExport($this->projectId, $this->datedebut, $this->datefin),
            new CallExport($this->projectId, $this->datedebut, $this->datefin)
        ];
        return $sheets;
    }
}